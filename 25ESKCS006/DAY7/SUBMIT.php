<?php

$name=$_POST['name'];
$email=$_POST['email'];
$age=$_POST['age'];
$phone=$_POST['phone'];
$gender=$_POST['gender'] ?? "";
$course=$_POST['course'];
$address=$_POST['address'];

$errors=[];


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
    echo "<h2 style='color:red;'>Validation Errors</h2>";

    echo "<div style='background:#ffe5e5;padding:15px;border-radius:10px;'>";

    echo "<ul>";

    foreach($errors as $e)
    {
        echo "<li>$e</li>";
    }

    echo "</ul>";

    echo "<a href='index.php'>Go Back</a>";

    echo "</div>";

    exit;
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Registration Successful</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow p-4">

<h2 class="text-success text-center">
Registration Successful
</h2>


<?php echo $name; ?>

<?php echo $email; ?>

<?php echo $age; ?>



<?php echo $phone; ?>



<?php echo $gender; ?>



<?php echo $course; ?>




<?php echo $address; ?>




<?php echo $photo; ?>

</div>

</div>

</body>

</html>