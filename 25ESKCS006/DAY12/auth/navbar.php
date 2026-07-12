<?php
// Include this at the top of any page that requires a logged-in user.
// Expects session_start() and db_connect.php to already be included by
// the parent page, and $conn to be available.

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT name, profile_picture FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$currentUser = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$avatarSrc = $currentUser['profile_picture']
    ? "uploads/profiles/" . htmlspecialchars($currentUser['profile_picture'])
    : "https://via.placeholder.com/40x40.png?text=%20";
?>
<nav class="navbar navbar-expand navbar-light bg-light border-bottom px-3 mb-4">
    <a class="navbar-brand" href="profile.php">Student Portal</a>

    <div class="ms-auto d-flex align-items-center">
        <img src="<?php echo $avatarSrc; ?>"
             width="36" height="36"
             style="border-radius:50%;object-fit:cover;margin-right:10px;">

        <span class="me-3"><?php echo htmlspecialchars($currentUser['name']); ?></span>

        <a href="profile.php" class="btn btn-sm btn-outline-primary me-2">Profile</a>
        <a href="change_password.php" class="btn btn-sm btn-outline-secondary me-2">Change Password</a>
        <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>
</nav>
