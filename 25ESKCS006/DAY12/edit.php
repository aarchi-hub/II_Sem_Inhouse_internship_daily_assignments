<?php
require 'auth_check.php';
include 'db_connect.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$successMsg = '';

// Handle the update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $age     = $_POST['age'] ?? '';
    $phone   = trim($_POST['phone'] ?? '');
    $gender  = $_POST['gender'] ?? '';
    $course  = $_POST['course'] ?? '';
    $status  = $_POST['status'] ?? 'Active';
    $address = trim($_POST['address'] ?? '');

    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Name should contain only letters.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email.";
    }

    if (empty($age))    $errors[] = "Age is required.";
    if (empty($phone))  $errors[] = "Phone number is required.";
    if (empty($gender)) $errors[] = "Select Gender.";
    if (empty($course)) $errors[] = "Select Course.";
    if (strlen($address) < 10) $errors[] = "Address must contain minimum 10 characters.";

    // Optional new photo
    $photoUpdate = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK && $_FILES['photo']['name'] != "") {
        $originalName = $_FILES['photo']['name'];
        $tmpPath      = $_FILES['photo']['tmp_name'];
        $fileSize     = $_FILES['photo']['size'];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize        = 2 * 1024 * 1024;

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            $errors[] = "Photo must be a JPG, JPEG, PNG, or GIF file.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "Photo must be smaller than 2MB.";
        } else {
            $photoUpdate = uniqid('photo_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            move_uploaded_file($tmpPath, $uploadDir . $photoUpdate);
        }
    }

    if (empty($errors)) {
        // Build the field list, types, and values together so the type
        // string always matches the parameter count/order exactly —
        // safer than hand-counting "ssiss..." characters.
        $fields = ['name', 'email', 'age', 'phone', 'gender', 'course', 'status', 'address'];
        $values = [$name, $email, $age, $phone, $gender, $course, $status, $address];
        $types  = "ssisssss"; // s,s,i,s,s,s,s,s — matches $fields order above

        if ($photoUpdate) {
            $fields[] = 'photo';
            $values[] = $photoUpdate;
            $types   .= "s";
        }

        $setClause = implode(", ", array_map(fn($f) => "$f = ?", $fields));
        $sql = "UPDATE submission SET $setClause WHERE id = ?";

        $values[] = $id;
        $types   .= "i";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$values);

        if (mysqli_stmt_execute($stmt)) {
            $successMsg = "Student record updated successfully.";
        } else {
            $errors[] = "Update failed: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch current record (after any update, so the form shows fresh data)
$stmt = mysqli_prepare($conn, "SELECT * FROM submission WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width:700px;">
<div class="card shadow p-4">

<h2 class="text-primary mb-4"><i class="bi bi-pencil-square"></i> Edit Student</h2>

<?php if ($successMsg) { ?>
<div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
<?php } ?>

<?php if (!empty($errors)) { ?>
<div class="alert alert-danger">
<ul class="mb-0">
<?php foreach ($errors as $e) { ?>
<li><?php echo htmlspecialchars($e); ?></li>
<?php } ?>
</ul>
</div>
<?php } ?>

<div class="text-center mb-3">
<img src="uploads/<?php echo htmlspecialchars($student['photo']); ?>" width="100" height="100" style="border-radius:50%;object-fit:cover;">
</div>

<form method="POST" enctype="multipart/form-data">

<div class="row">
<div class="col-md-6 mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>">
</div>
<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>">
</div>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label>Age</label>
<input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($student['age']); ?>">
</div>
<div class="col-md-6 mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
</div>
</div>

<div class="mb-3">
<label>Gender</label>
<div class="form-check form-check-inline">
<input type="radio" class="form-check-input" name="gender" value="Male" <?php echo $student['gender']==='Male'?'checked':''; ?>>
<label class="form-check-label">Male</label>
</div>
<div class="form-check form-check-inline">
<input type="radio" class="form-check-input" name="gender" value="Female" <?php echo $student['gender']==='Female'?'checked':''; ?>>
<label class="form-check-label">Female</label>
</div>
<div class="form-check form-check-inline">
<input type="radio" class="form-check-input" name="gender" value="Other" <?php echo $student['gender']==='Other'?'checked':''; ?>>
<label class="form-check-label">Other</label>
</div>
</div>

<div class="mb-3">
<label>Status</label>
<select name="status" class="form-select">
<option value="Active" <?php echo $student['status']==='Active'?'selected':''; ?>>Active</option>
<option value="Inactive" <?php echo $student['status']==='Inactive'?'selected':''; ?>>Inactive</option>
</select>
</div>

<div class="mb-3">
<label>Course</label>
<select name="course" class="form-select">
<?php foreach (['BCA','B.Tech','BBA','MCA','MBA'] as $c) { ?>
<option <?php echo $student['course']===$c?'selected':''; ?>><?php echo $c; ?></option>
<?php } ?>
</select>
</div>

<div class="mb-3">
<label>Address</label>
<textarea name="address" rows="4" class="form-control"><?php echo htmlspecialchars($student['address']); ?></textarea>
</div>

<div class="mb-3">
<label>Replace Profile Photo (optional)</label>
<input type="file" name="photo" class="form-control">
</div>

<div class="d-flex gap-2">
<button class="btn btn-primary px-5"><i class="bi bi-save"></i> Save Changes</button>
<a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
</div>

</form>

</div>
</div>

</body>
</html>
