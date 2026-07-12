<?php 
include '25ESKCS006\DAY10\db_connect.php';

$name=$_POST['name'];
$email=$_POST['email'];
$age=$_POST['age'];
$phone=$_POST['phone'];
$gender=$_POST['gender'] ?? "";
$course=$_POST['course'];
$address=$_POST['address'];
$photo = $_FILES['photo']['name'];
$tmp = $_FILES['photo']['tmp_name'];
$errors=[];
move_uploaded_file($tmp,"uploads/".$photo);



if(empty($name))
{
    $errors[]="Name is required.";
}
elseif(!preg_match("/^[a-zA-Z ]+$/",$name))
{
    $errors[]="Name should contain only letters.";
}


if(empty($email))
{
    $errors[]="Email is required.";
}
elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))
{
    $errors[]="Invalid Email.";
}


if(empty($age))
{
    $errors[]="Age is required.";
}


if(empty($phone))
{
    $errors[]="Phone number is required.";
}


if(empty($gender))
{
    $errors[]="Select Gender.";
}


if(empty($course))
{
    $errors[]="Select Course.";
}


if(strlen(trim($address))<10)
{
    $errors[]="Address must contain minimum 10 characters.";
}


$photo="No Photo Selected";

if(isset($_FILES['photo']) && $_FILES['photo']['name']!="")
{
    $photo=$_FILES['photo']['name'];
}


if(count($errors)>0)
{
    // ---- VALIDATION FAILED: show errors, do NOT insert ----
    echo "<h2 style='color:red;'>Validation Errors</h2>";
    echo "<div style='background:#ffe5e5;padding:15px;border-radius:10px;'>";
    echo "<ul>";
    foreach($errors as $e)
    {
        echo "<li>$e</li>";
    }
    echo "</ul>";
    echo "<a href='index.html'>Go Back</a>";
    echo "</div>";
}
else
{
    // ---- VALIDATION PASSED: insert into the database ----
    // Use a prepared statement so user input can never break out of the query (SQL injection safe)
    $sql = "INSERT INTO submission (name, email, age, phone, gender, course, address, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if($stmt)
    {
        // "ssisssss" = 8 letters for 8 params: name(s) email(s) age(i) phone(s) gender(s) course(s) address(s) photo(s)
        mysqli_stmt_bind_param($stmt, "ssisssss", $name, $email, $age, $phone, $gender, $course, $address, $photo);

        if(mysqli_stmt_execute($stmt))
        {
            echo "<h2 style='color:green;'>Data Inserted Successfully</h2>";
            echo "<a href='index.html'>Go Back</a>";
        }
        else
        {
            echo "Error: ".mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
    else
    {
        echo "Prepare failed: ".mysqli_error($conn);
    }
}


mysqli_close($conn);
?>