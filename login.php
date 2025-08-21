<?php
// login.php
include 'config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else $err = "Invalid credentials.";
    } else $err = "User not found.";
}
$registered = isset($_GET['registered']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:600px;">
  <h3>Login</h3>
  <?php if($registered): ?><div class="alert alert-success">Registration successful. Please login.</div><?php endif; ?>
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
    <button class="btn btn-primary">Login</button>
    <a class="btn btn-link" href="register.php">Register</a>
  </form>
</div>
</body>
</html>
