<?php
// delete_biodata.php
include 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// get photo name to unlink
$stmt = $conn->prepare("SELECT photo FROM biodata WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows) {
    $r = $res->fetch_assoc();
    if ($r['photo'] && file_exists(UPLOAD_DIR . $r['photo'])) unlink(UPLOAD_DIR . $r['photo']);
}

// delete
$stmt2 = $conn->prepare("DELETE FROM biodata WHERE id = ? AND user_id = ?");
$stmt2->bind_param("ii", $id, $user_id);
$stmt2->execute();

header("Location: dashboard.php");
exit;
