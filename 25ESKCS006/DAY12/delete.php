<?php
require 'auth_check.php';
include 'db_connect.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {

    // Look up the photo filename first so we can remove it from disk too.
    $stmt = mysqli_prepare($conn, "SELECT photo FROM submission WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $stmt = mysqli_prepare($conn, "DELETE FROM submission WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Clean up the uploaded photo file, if any and not the placeholder.
        if (!empty($row['photo']) && $row['photo'] !== 'No Photo Selected') {
            $photoPath = __DIR__ . '/uploads/' . $row['photo'];
            if (is_file($photoPath)) {
                unlink($photoPath);
            }
        }
    }
}

header("Location: dashboard.php");
exit;
?>
