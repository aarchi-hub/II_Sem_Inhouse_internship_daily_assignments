<?php
// FIXED: was including an absolute Windows desktop path that only exists
// on your machine. db_connect.php lives in the same folder as this file.
include 'C:\Users\Aarchi\OneDrive\Desktop\training session\II_Sem_Inhouse_internship_daily_assignments\25ESKCS006\DAY11\db_connect.php';

$total  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission"));
$male   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission WHERE gender='Male'"));
$female = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission WHERE gender='Female'"));
$active = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission WHERE status='Active'"));

// FIXED (SQL injection): $course and $search used to be concatenated
// directly into the SQL string, e.g. "WHERE course='$course'". Anyone
// could put something like  BCA' OR '1'='1  into the course/search box
// and dump or manipulate the whole table. Now both filters use a
// prepared statement with bound parameters.
//
// FIXED (dead filter): previously the course filter's $where was
// immediately overwritten by the search block below it, so picking a
// course did nothing once the search form existed. Now both filters
// are combined and applied together.

$course = $_GET['course'] ?? '';
$search = $_GET['search'] ?? '';

$conditions = [];
$params     = [];
$types      = "";

if ($course !== '') {
    $conditions[] = "course = ?";
    $params[]     = $course;
    $types       .= "s";
}

if ($search !== '') {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $likeTerm     = "%" . $search . "%";
    $params[]     = $likeTerm;
    $params[]     = $likeTerm;
    $params[]     = $likeTerm;
    $types       .= "sss";
}

$sql = "SELECT * FROM submission";
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = mysqli_prepare($conn, $sql);

if (count($params) > 0) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
<h3><?php echo $total; ?></h3>
Total Students
</div>
</div>

<div class="col-md-3">
<div class="card bg-success text-white p-3">
<h3><?php echo $male; ?></h3>
Male
</div>
</div>

<div class="col-md-3">
<div class="card bg-danger text-white p-3">
<h3><?php echo $female; ?></h3>
Female
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning p-3">
<h3><?php echo $active; ?></h3>
Active
</div>
</div>

<form method="GET">

<select name="course">
<option value="">All Courses</option>
<option <?php echo $course === 'BCA' ? 'selected' : ''; ?>>BCA</option>
<option <?php echo $course === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
<option <?php echo $course === 'BBA' ? 'selected' : ''; ?>>BBA</option>
<option <?php echo $course === 'MCA' ? 'selected' : ''; ?>>MCA</option>
<option <?php echo $course === 'MBA' ? 'selected' : ''; ?>>MBA</option>
</select>

<button class="btn btn-primary">Filter</button>

</form>

<form method="GET">

<input type="text"
name="search"
placeholder="Search Name, Email, Phone"
value="<?php echo htmlspecialchars($search); ?>">

<button class="btn btn-success">Search</button>

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

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td>
<img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>"
width="60"
height="60">
</td>

<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['course']); ?></td>
<td><?php echo htmlspecialchars($row['status']); ?></td>

<td>
<a href="edit.php?id=<?php echo (int)$row['id']; ?>"
class="btn btn-warning btn-sm">
Edit
</a>

<a href="delete.php?id=<?php echo (int)$row['id']; ?>"
class="btn btn-danger btn-sm">
Delete
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>