<?php
session_start();
include 'C:\Users\Aarchi\OneDrive\Desktop\training session\II_Sem_Inhouse_internship_daily_assignments\25ESKCS006\DAY11\db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check email isn't already taken
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "An account with that email already exists.";
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($errors)) {
        // Never store plain-text passwords — hash with bcrypt.
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Account created successfully. Please log in.";
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:480px;">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary mb-4">Create Account</h2>

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
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <div class="text-center">
                <button class="btn btn-primary px-5">Register</button>
            </div>
        </form>

        <p class="text-center mt-3">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</div>
</body>
</html>