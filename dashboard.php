<?php
// dashboard.php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch rows for this user
$stmt = $conn->prepare("SELECT id, full_name, age, gender, religion, education, occupation, photo, created_at FROM biodata WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Welcome, <?= e($_SESSION['username']) ?></h3>
    <div>
      <a class="btn btn-success" href="add_biodata.php">Add New Bio-data</a>
      <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
    </div>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>Photo</th>
        <th>Full Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Religion</th>
        <th>Education</th>
        <th>Occupation</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td style="width:80px;">
          <?php if($row['photo'] && file_exists(UPLOAD_DIR . $row['photo'])): ?>
            <img src="<?= e(UPLOAD_URL . $row['photo']) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:4px;">
          <?php else: ?>
            <div style="width:70px;height:70px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#888;">No Photo</div>
          <?php endif; ?>
        </td>
        <td><?= e($row['full_name']) ?></td>
        <td><?= e($row['age']) ?></td>
        <td><?= e($row['gender']) ?></td>
        <td><?= e($row['religion']) ?></td>
        <td><?= e($row['education']) ?></td>
        <td><?= e($row['occupation']) ?></td>
        <td><?= e($row['created_at']) ?></td>
        <td>
          <a class="btn btn-sm btn-primary" href="view_biodata.php?id=<?= $row['id'] ?>">View</a>
          <a class="btn btn-sm btn-warning" href="edit_biodata.php?id=<?= $row['id'] ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="delete_biodata.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
