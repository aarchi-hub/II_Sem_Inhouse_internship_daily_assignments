<?php

include 'C:\Users\Aarchi\OneDrive\Desktop\training session\II_Sem_Inhouse_internship_daily_assignments\25ESKCS006\DAY11\db_connect.php';

$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$age     = $_POST['age'] ?? '';
$phone   = $_POST['phone'] ?? '';
$gender  = $_POST['gender'] ?? '';
$course  = $_POST['course'] ?? '';
$address = $_POST['address'] ?? '';

$errors = [];

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

if (empty($age)) {
    $errors[] = "Age is required.";
}

if (empty($phone)) {
    $errors[] = "Phone number is required.";
}

if (empty($gender)) {
    $errors[] = "Select Gender.";
}

if (empty($course)) {
    $errors[] = "Select Course.";
}

if (strlen(trim($address)) < 10) {
    $errors[] = "Address must contain minimum 10 characters.";
}

// ---- Photo handling ----
// FIXED: previously the file was move_uploaded_file()'d to uploads/ at the
// very top of the script, BEFORE any validation ran. That meant a submission
// with a missing name or a bad email still saved the uploaded file to disk.
// Now we only validate + save the file once everything else has passed.
$photo = "No Photo Selected";
$photoUploaded = false;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK && $_FILES['photo']['name'] != "") {

    $originalName = $_FILES['photo']['name'];
    $tmpPath      = $_FILES['photo']['tmp_name'];
    $fileSize     = $_FILES['photo']['size'];

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize        = 2 * 1024 * 1024; // 2MB

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExtensions)) {
        // FIXED: previously ANY file type was accepted and saved with its
        // original name — someone could upload "shell.php" as a "photo"
        // and, depending on server config, execute it. Now only image
        // extensions are allowed.
        $errors[] = "Photo must be a JPG, JPEG, PNG, or GIF file.";
    } elseif ($fileSize > $maxFileSize) {
        $errors[] = "Photo must be smaller than 2MB.";
    } else {
        // Generate a safe, unique filename instead of trusting the
        // user-supplied original filename directly.
        $photo = uniqid('photo_', true) . '.' . $ext;
        $photoUploaded = true;
    }
}

if (count($errors) > 0) {
    // ---- VALIDATION FAILED: show errors, do NOT insert, do NOT save file ----
    echo "<h2 style='color:red;'>Validation Errors</h2>";
    echo "<div style='background:#ffe5e5;padding:15px;border-radius:10px;'>";
    echo "<ul>";
    foreach ($errors as $e) {
        // FIXED: escape error text before echoing (defense in depth).
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo "</ul>";
    echo "<a href='index.html'>Go Back</a>";
    echo "</div>";
} else {
    // ---- VALIDATION PASSED: now it's safe to move the uploaded file ----
    if ($photoUploaded) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        move_uploaded_file($tmpPath, $uploadDir . $photo);
    }

    // ---- Insert into the database using a prepared statement ----
    $sql = "INSERT INTO submission (name, email, age, phone, gender, course, address, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // "ssisssss" = 8 params: name(s) email(s) age(i) phone(s) gender(s) course(s) address(s) photo(s)
        mysqli_stmt_bind_param($stmt, "ssisssss", $name, $email, $age, $phone, $gender, $course, $address, $photo);

        if (mysqli_stmt_execute($stmt)) {
            echo "<h2 style='color:green;'>Data Inserted Successfully</h2>";
            echo "<a href='index.html'>Go Back</a>";
        } else {
            echo "Error: " . htmlspecialchars(mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Prepare failed: " . htmlspecialchars(mysqli_error($conn));
    }
}

mysqli_close($conn);
?>