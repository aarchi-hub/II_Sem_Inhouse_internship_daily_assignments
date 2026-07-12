<?php
include 'C:\Users\Aarchi\OneDrive\Desktop\training session\II_Sem_Inhouse_internship_daily_assignments\25ESKCS006\DAY10\db_connect.php';

$total=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM submission"));

$male=mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM submission WHERE gender='Male'"));

$female=mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM submission WHERE gender='Female'"));

$active=mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM submission WHERE status='Active'"));

$where="";

if(isset($_GET['course']) && $_GET['course']!="")
{
    $course=$_GET['course'];
    $where=" WHERE course='$course'";
}

$result=mysqli_query($conn,"SELECT * FROM submission".$where);

$where="";

if(isset($_GET['search']))
{
    $search=$_GET['search'];

    $where=" WHERE
    name LIKE '%$search%'
    OR email LIKE '%$search%'
    OR phone LIKE '%$search%'
    ";
}

$result=mysqli_query($conn,"SELECT * FROM submission".$where);
?>

<!DOCTYPE html>
<html>
<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="row">

<div class="col-md-3">
<div class="card bg-primary text-white p-3">
<h3><?php echo $total;?></h3>
Total Students
</div>
</div>

<div class="col-md-3">
<div class="card bg-success text-white p-3">
<h3><?php echo $male;?></h3>
Male
</div>
</div>

<div class="col-md-3">
<div class="card bg-danger text-white p-3">
<h3><?php echo $female;?></h3>
Female
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning p-3">
<h3><?php echo $active;?></h3>
Active
</div>
</div>
<form method="GET">

<select name="course">

<option value="">All Courses</option>
<option>BCA</option>
<option>B.Tech</option>
<option>BBA</option>
<option>MCA</option>
<option>MBA</option>

</select>

<button class="btn btn-primary">
Filter
</button>

</form>
<form method="GET">

<input type="text"
name="search"
placeholder="Search Name, Email, Phone">

<button class="btn btn-success">
Search
</button>

</form>
<table class="table table-bordered table-hover">

<tr>

<th>Photo</th>
<th>Name</th>
<th>Email</th>
<th>Course</th>
<th>Status</th>
<th>Action</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td>

<img src="uploads/<?php echo $row['photo'];?>"
width="60"
height="60">

</td>

<td><?php echo $row['name'];?></td>

<td><?php echo $row['email'];?></td>

<td><?php echo $row['course'];?></td>

<td><?php echo $row['status'];?></td>

<td>

<a href="edit.php?id=<?php echo $row['id'];?>"
class="btn btn-warning btn-sm">
Edit
</a>

<a href="delete.php?id=<?php echo $row['id'];?>"
class="btn btn-danger btn-sm">
Delete
</a>

</td>

</tr>

<?php
}
?>

</table>

</div>

</div>

</body>
</html>