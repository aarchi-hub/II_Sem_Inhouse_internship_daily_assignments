<?php
// Per the assignment: UI only — form with an email input and a
// confirmation message. No actual email sending is required.
session_start();

$submitted = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $submitted = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width:420px;">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary mb-4">Forgot Password</h2>

        <?php if ($submitted) { ?>
            <div class="alert alert-success">
                If an account exists for <strong><?php echo htmlspecialchars($email); ?></strong>,
                a password reset link has been sent to that email address.
            </div>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary px-4">Back to Login</a>
            </div>
        <?php } else { ?>

            <p class="text-muted">Enter your account email and we'll send you a link to reset your password.</p>

            <form method="POST">
                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="text-center">
                    <button class="btn btn-primary px-5">Send Reset Link</button>
                </div>
            </form>

            <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>

        <?php } ?>
    </div>
</div>

</body>
</html>