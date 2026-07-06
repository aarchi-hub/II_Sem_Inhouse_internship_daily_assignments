<?php
$name = $email = $cgpa = $branch = $college = "";
$grade = "";
$message = "";
$card = false;

function calculateGrade($cgpa)
{
    if ($cgpa >= 9)
        return ["Excellent", "success"];
    elseif ($cgpa >= 8)
        return ["Very Good", "primary"];
    elseif ($cgpa >= 7)
        return ["Good", "info"];
    elseif ($cgpa >= 6)
        return ["Average", "warning"];
    else
        return ["Needs Improvement", "danger"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $cgpa = trim($_POST["cgpa"]);
    $branch = trim($_POST["branch"]);
    $college = trim($_POST["college"]);

    if (empty($name) || empty($email) || empty($cgpa) || empty($branch) || empty($college)) {
        $message = "<div class='alert alert-danger'>Please fill all fields.</div>";
    } else {
        $result = calculateGrade($cgpa);
        $grade = $result[0];
        $color = $result[1];
        $card = true;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Registration Confirmation</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f5f7fb;
        }

        .header{
            background:#0d6efd;
            color:white;
            padding:20px;
            text-align:center;
        }

        .footer{
            background:#0d6efd;
            color:white;
            text-align:center;
            padding:10px;
            margin-top:30px;
        }

        .card-box{
            background:white;
            padding:25px;
            border-radius:12px;
            box-shadow:0px 0px 10px rgba(0,0,0,.2);
        }

        .confirm-card{
            margin-top:30px;
            background:linear-gradient(135deg,#4facfe,#00f2fe);
            color:white;
            border-radius:15px;
            padding:30px;
            box-shadow:0px 5px 15px rgba(0,0,0,.3);
        }

        .profile{
            width:100px;
            height:100px;
            border-radius:50%;
            background:white;
            color:#0d6efd;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:40px;
            margin:auto;
            margin-bottom:20px;
        }

    </style>

</head>

<body>

<div class="header">
    <h2>Student Registration Confirmation System</h2>
</div>

<div class="container mt-5">

<div class="card-box">

<h3 class="text-center mb-4">Student Registration Form</h3>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
</div>

<div class="mb-3">
<label>CGPA</label>
<input type="number" step="0.01" name="cgpa" class="form-control" value="<?php echo $cgpa; ?>">
</div>

<div class="mb-3">
<label>Branch</label>
<select class="form-select" name="branch">
<option value="">Select</option>
<option <?php if($branch=="CSE") echo "selected"; ?>>CSE</option>
<option <?php if($branch=="IT") echo "selected"; ?>>IT</option>
<option <?php if($branch=="ECE") echo "selected"; ?>>ECE</option>
<option <?php if($branch=="Mechanical") echo "selected"; ?>>Mechanical</option>
<option <?php if($branch=="Civil") echo "selected"; ?>>Civil</option>
</select>
</div>

<div class="mb-3">
<label>College</label>
<input type="text" name="college" class="form-control" value="<?php echo $college; ?>">
</div>

<div class="text-center">
<button class="btn btn-primary px-5">Register</button>
</div>

</form>

</div>

<?php

if($card){

echo "

<div class='confirm-card'>

<div class='profile'>
👤
</div>

<h2 class='text-center'>Registration Successful</h2>

<hr>

<p><b>Name :</b> $name</p>

<p><b>Email :</b> $email</p>

<p><b>CGPA :</b> $cgpa</p>

<p><b>Branch :</b> $branch</p>

<p><b>College :</b> $college</p>

<div class='alert alert-$color mt-3'>
<b>Grade :</b> $grade
</div>

<p><b>Date :</b> ".date("d-m-Y")."</p>

</div>

";

}

?>

</div>

<div class="footer">
<?php echo date("Y"); ?> Student Registration System
</div>

</body>
</html>