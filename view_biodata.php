<?php
// view_biodata.php
include 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM biodata WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { die("Not found or access denied."); }
$row = $res->fetch_assoc();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>View Bio-data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <a class="btn btn-secondary mb-3" href="dashboard.php">Back</a>
  <div class="card p-3">
    <div class="d-flex gap-3">
      <div style="flex:0 0 160px;">
        <?php if($row['photo'] && file_exists(UPLOAD_DIR . $row['photo'])): ?>
          <img src="<?= e(UPLOAD_URL . $row['photo']) ?>" style="width:160px;height:160px;object-fit:cover;border-radius:6px;">
        <?php else: ?>
          <div style="width:160px;height:160px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#888;">No Photo</div>
        <?php endif; ?>
      </div>
      <div style="flex:1;">
        <h4><?= e($row['full_name']) ?> <small class="text-muted"><?= e($row['age']) ?> yrs</small></h4>
        <p><strong>Gender:</strong> <?= e($row['gender']) ?> &nbsp; <strong>Religion:</strong> <?= e($row['religion']) ?></p>
        <p><strong>Education:</strong> <?= e($row['education']) ?> &nbsp; <strong>Occupation:</strong> <?= e($row['occupation']) ?></p>
        <p><strong>Contact:</strong> <?= e($row['phone']) ?> | <?= e($row['email']) ?></p>
        <p><strong>Address:</strong> <?= nl2br(e($row['address'])) ?></p>
        <hr>
        <p><strong>About:</strong><br><?= nl2br(e($row['about_me'])) ?></p>
        <p><strong>Hobbies:</strong> <?= nl2br(e($row['hobbies'])) ?></p>
        <p><strong>Expectations:</strong> <?= nl2br(e($row['expectations'])) ?></p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
