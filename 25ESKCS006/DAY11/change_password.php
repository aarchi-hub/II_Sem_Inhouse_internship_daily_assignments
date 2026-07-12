<?php
session_start();
include 'C:\Users\Aarchi\OneDrive\Desktop\training session\II_Sem_Inhouse_internship_daily_assignments\25ESKCS006\DAY11\db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Fetch the user's current hashed password to verify against
    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errors[] = "All fields are required.";
    } elseif (!password_verify($currentPassword, $user['password'])) {
        $errors[] = "Current password is incorrect.";
    } elseif (strlen($newPassword) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "New password and confirmation do not match.";
    } elseif ($newPassword === $currentPassword) {
        $errors[] = "New password must be different from the current password.";
    }

    if (empty($errors)) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashed, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $successMsg = "Password changed successfully.";
    }
}

include 'navbar.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container" style="max-width:460px;">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary mb-4">Change Password</h2>

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

        <form method="POST">
            <div class="mb-3">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            <div class="mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <div class="text-center">
                <button class="btn btn-primary px-5">Update Password</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>