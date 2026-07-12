<?php
session_start();
include 'C:\Users\Aarchi\OneDrive\Desktop\training session\II_Sem_Inhouse_internship_daily_assignments\25ESKCS006\DAY11\db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$successMsg = '';

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {

    if ($_FILES['photo']['error'] === UPLOAD_ERR_OK && $_FILES['photo']['name'] != "") {

        $originalName = $_FILES['photo']['name'];
        $tmpPath      = $_FILES['photo']['tmp_name'];
        $fileSize     = $_FILES['photo']['size'];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize        = 2 * 1024 * 1024; // 2MB

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            $errors[] = "Photo must be a JPG, JPEG, PNG, or GIF file.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "Photo must be smaller than 2MB.";
        } else {
            $filename = uniqid('avatar_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/profiles/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($tmpPath, $uploadDir . $filename)) {
                $stmt = mysqli_prepare($conn, "UPDATE users SET profile_picture = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $filename, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $successMsg = "Profile picture updated.";
            } else {
                $errors[] = "Could not save the uploaded file.";
            }
        }
    } else {
        $errors[] = "Please choose a photo to upload.";
    }
}

$stmt = mysqli_prepare($conn, "SELECT name, email, profile_picture FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$avatarSrc = $user['profile_picture']
    ? "uploads/profiles/" . htmlspecialchars($user['profile_picture'])
    : "https://via.placeholder.com/150x150.png?text=No+Photo";

include 'navbar.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container" style="max-width:520px;">
    <div class="card shadow p-4 text-center">

        <img src="<?php echo $avatarSrc; ?>"
             width="150" height="150"
             style="border-radius:50%;object-fit:cover;margin:0 auto 20px;">

        <h3><?php echo htmlspecialchars($user['name']); ?></h3>
        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

        <?php if ($successMsg) { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php } ?>

        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger">
                <ul class="mb-0 text-start">
                    <?php foreach ($errors as $e) { ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3 text-start">
                <label>Upload New Profile Picture</label>
                <input type="file" name="photo" class="form-control">
            </div>
            <button class="btn btn-primary">Update Photo</button>
        </form>

    </div>
</div>

</body>
</html>