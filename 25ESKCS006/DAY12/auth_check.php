<?php
// SESSION GUARD — include this at the very top of any page that should
// only be reachable by a logged-in user (dashboard, edit, delete).
// Safe to include even if session_start() was already called elsewhere.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}
?>
