<?php
session_start();
include 'db_connect.php';

$errors = [];

// If already logged in, skip straight to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$successMsg = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID on login to prevent session fixation.
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: ../dashboard.php");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:420px;">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary mb-4">Log In</h2>

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
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="text-end mb-3">
                <a href="forgot_password.php">Forgot password?</a>
            </div>
            <div class="text-center">
                <button class="btn btn-primary px-5">Log In</button>
            </div>
        </form>

        <p class="text-center mt-3">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>
</body>
</html>
