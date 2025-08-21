<?php
// register.php
include 'config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3 || strlen($password) < 4) {
        $err = "Username must be >=3 chars and password >=4 chars.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);
        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            if ($conn->errno === 1062) $err = "Username already exists.";
            else $err = "Error: " . $conn->error;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:600px;">
  <h3>Register</h3>
  <?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input class="form-control" name="username" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" class="form-control" name="password" required>
    </div>
    <button class="btn btn-primary">Register</button>
    <a class="btn btn-link" href="login.php">Login</a>
  </form>
</div>
</body>
</html>
