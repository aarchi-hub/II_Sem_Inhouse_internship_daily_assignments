<?php
require 'auth_check.php';   // must be logged in to view this page
include 'db_connect.php';

$total  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission"));
$male   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission WHERE gender='Male'"));
$female = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission WHERE gender='Female'"));
$active = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM submission WHERE status='Active'"));

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
$sql .= " ORDER BY id DESC";

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
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background:#f4f6f9; }
        .stat-card { border:none; border-radius:16px; }
        .stat-card i { font-size:28px; opacity:.85; }
        .table-responsive { border-radius:12px; overflow:hidden; }
        .avatar-thumb { width:48px; height:48px; object-fit:cover; border-radius:50%; }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-4 mb-4">
    <span class="navbar-brand mb-0 h1"><i class="bi bi-mortarboard-fill text-primary"></i> Student Management</span>
    <div>
        <span class="me-3 text-muted">Logged in as <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
        <a href="auth/change_password.php" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-key"></i> Change Password</a>
        <a href="auth/logout.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</nav>

<div class="container">

<div class="row g-3 mb-4">

<div class="col-6 col-md-3">
<div class="card stat-card bg-primary text-white p-3">
<div class="d-flex justify-content-between align-items-center">
<div><h3 class="mb-0"><?php echo $total; ?></h3><span>Total Students</span></div>
<i class="bi bi-people-fill"></i>
</div>
</div>
</div>

<div class="col-6 col-md-3">
<div class="card stat-card bg-success text-white p-3">
<div class="d-flex justify-content-between align-items-center">
<div><h3 class="mb-0"><?php echo $male; ?></h3><span>Male</span></div>
<i class="bi bi-gender-male"></i>
</div>
</div>
</div>

<div class="col-6 col-md-3">
<div class="card stat-card bg-danger text-white p-3">
<div class="d-flex justify-content-between align-items-center">
<div><h3 class="mb-0"><?php echo $female; ?></h3><span>Female</span></div>
<i class="bi bi-gender-female"></i>
</div>
</div>
</div>

<div class="col-6 col-md-3">
<div class="card stat-card bg-warning p-3">
<div class="d-flex justify-content-between align-items-center">
<div><h3 class="mb-0"><?php echo $active; ?></h3><span>Active</span></div>
<i class="bi bi-check-circle-fill"></i>
</div>
</div>
</div>

</div>

<div class="card p-3 mb-4">
<div class="row g-2 align-items-end">

<form method="GET" class="col-12 col-md-5 d-flex gap-2">
<select name="course" class="form-select">
<option value="">All Courses</option>
<option <?php echo $course === 'BCA' ? 'selected' : ''; ?>>BCA</option>
<option <?php echo $course === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
<option <?php echo $course === 'BBA' ? 'selected' : ''; ?>>BBA</option>
<option <?php echo $course === 'MCA' ? 'selected' : ''; ?>>MCA</option>
<option <?php echo $course === 'MBA' ? 'selected' : ''; ?>>MBA</option>
</select>
<button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
</form>

<form method="GET" class="col-12 col-md-5 d-flex gap-2">
<input type="text" name="search" class="form-control"
placeholder="Search Name, Email, Phone"
value="<?php echo htmlspecialchars($search); ?>">
<button class="btn btn-success"><i class="bi bi-search"></i> Search</button>
</form>

<div class="col-12 col-md-2 text-md-end">
<a href="index.html" class="btn btn-outline-primary w-100"><i class="bi bi-plus-lg"></i> Add Student</a>
</div>

</div>
</div>

<div class="card p-0">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead class="table-light">
<tr>
<th>Photo</th>
<th>Name</th>
<th>Email</th>
<th>Course</th>
<th>Status</th>
<th class="text-end">Action</th>
</tr>
</thead>
<tbody>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>
<td>
<img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" class="avatar-thumb">
</td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['course']); ?></span></td>
<td>
<?php if ($row['status'] === 'Active') { ?>
<span class="badge bg-success">Active</span>
<?php } else { ?>
<span class="badge bg-secondary">Inactive</span>
<?php } ?>
</td>
<td class="text-end">
<a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-warning btn-sm">
<i class="bi bi-pencil-square"></i> Edit
</a>
<a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Delete this student record? This cannot be undone.');">
<i class="bi bi-trash"></i> Delete
</a>
</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>

</div>

</body>
</html>
