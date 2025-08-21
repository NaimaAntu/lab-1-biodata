<?php
// edit_biodata.php
include 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM biodata WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { die("Record not found or access denied."); }
$row = $res->fetch_assoc();

$fields = [
    'full_name','dob','age','gender','religion','caste','nationality','phone','email','address',
    'height','weight','blood_group','complexion','education','occupation','annual_income','work_location',
    'father_name','father_occupation','mother_name','mother_occupation','siblings','preferred_age_min','preferred_age_max',
    'preferred_education','preferred_location','hobbies','about_me','expectations'
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // gather inputs
    $data = [];
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');

    // photo optional
    $photoName = $row['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $maxSize = 2 * 1024 * 1024;
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $errors[] = "Invalid photo format.";
        elseif ($_FILES['photo']['size'] > $maxSize) $errors[] = "Photo too large.";
        else {
            $photoName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_DIR . $photoName)) $errors[] = "Failed photo upload.";
            else {
                // remove old photo if exists
                if ($row['photo'] && file_exists(UPLOAD_DIR . $row['photo'])) unlink(UPLOAD_DIR . $row['photo']);
            }
        }
    }

    if (empty($data['full_name'])) $errors[] = "Full name required.";

    if (empty($errors)) {
        // build update SQL
        $sets = implode(", ", array_map(function($c){ return "$c = ?"; }, $fields)) . ", photo = ?";
        $sql = "UPDATE biodata SET $sets WHERE id = ? AND user_id = ?";
        $stmt2 = $conn->prepare($sql);
        $types = str_repeat("s", count($fields) + 1) . "ii";
        $params = array_merge(array_map(function($k) use ($data){ return $data[$k]; }, $fields), [$photoName, $id, $user_id]);
        $stmt2->bind_param($types, ...$params);
        if ($stmt2->execute()) {
            header("Location: dashboard.php");
            exit;
        } else $errors[] = "DB Error: " . $stmt2->error;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Bio-data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h3>Edit Bio-data</h3>
  <?php if($errors): foreach($errors as $er): ?><div class="alert alert-danger"><?= e($er) ?></div><?php endforeach; endif; ?>

  <form method="post" enctype="multipart/form-data">
    <!-- use the same layout as add_biodata, but fill values -->
    <div class="card p-3 mb-3">
      <h5>Personal Information</h5>
      <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Full Name *</label><input name="full_name" value="<?= e($row['full_name']) ?>" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">DOB</label><input type="date" name="dob" value="<?= e($row['dob']) ?>" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Age</label><input type="number" name="age" value="<?= e($row['age']) ?>" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Gender</label>
            <select name="gender" class="form-control">
              <option value="">Select</option>
              <option <?= $row['gender']=='Male' ? 'selected' : '' ?>>Male</option>
              <option <?= $row['gender']=='Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">Religion</label><input name="religion" value="<?= e($row['religion']) ?>" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Caste</label><input name="caste" value="<?= e($row['caste']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Nationality</label><input name="nationality" value="<?= e($row['nationality']) ?>" class="form-control"></div>
      </div>
    </div>

    <!-- contact -->
    <div class="card p-3 mb-3">
      <h5>Contact</h5>
      <div class="row g-2">
        <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" value="<?= e($row['phone']) ?>" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" value="<?= e($row['email']) ?>" class="form-control"></div>
        <div class="col-md-12"><label class="form-label">Address</label><textarea name="address" class="form-control"><?= e($row['address']) ?></textarea></div>
      </div>
    </div>

    <!-- (rest of fields follow same pattern - for brevity all fields follow same structure) -->
    <!-- Education & Career -->
    <div class="card p-3 mb-3">
      <h5>Education & Career</h5>
      <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Education</label><input name="education" value="<?= e($row['education']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Occupation</label><input name="occupation" value="<?= e($row['occupation']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Annual Income</label><input name="annual_income" value="<?= e($row['annual_income']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Work Location</label><input name="work_location" value="<?= e($row['work_location']) ?>" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Family Background</h5>
      <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Father's Name</label><input name="father_name" value="<?= e($row['father_name']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Father's Occupation</label><input name="father_occupation" value="<?= e($row['father_occupation']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Mother's Name</label><input name="mother_name" value="<?= e($row['mother_name']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Mother's Occupation</label><input name="mother_occupation" value="<?= e($row['mother_occupation']) ?>" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Siblings</label><input type="number" name="siblings" value="<?= e($row['siblings']) ?>" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Partner Preferences</h5>
      <div class="row g-2">
        <div class="col-md-3"><label class="form-label">Preferred Age Min</label><input type="number" name="preferred_age_min" value="<?= e($row['preferred_age_min']) ?>" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Preferred Age Max</label><input type="number" name="preferred_age_max" value="<?= e($row['preferred_age_max']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Preferred Education</label><input name="preferred_education" value="<?= e($row['preferred_education']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Preferred Location</label><input name="preferred_location" value="<?= e($row['preferred_location']) ?>" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Other Details</h5>
      <div class="mb-2"><label class="form-label">Hobbies</label><textarea name="hobbies" class="form-control"><?= e($row['hobbies']) ?></textarea></div>
      <div class="mb-2"><label class="form-label">About Me</label><textarea name="about_me" class="form-control"><?= e($row['about_me']) ?></textarea></div>
      <div class="mb-2"><label class="form-label">Expectations</label><textarea name="expectations" class="form-control"><?= e($row['expectations']) ?></textarea></div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Photo</h5>
      <div class="mb-2">
        <?php if($row['photo'] && file_exists(UPLOAD_DIR . $row['photo'])): ?>
          <img src="<?= e(UPLOAD_URL . $row['photo']) ?>" style="width:120px;height:120px;object-fit:cover;border-radius:4px;">
        <?php else: ?>
          <div style="width:120px;height:120px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#888;">No Photo</div>
        <?php endif; ?>
      </div>
      <div class="mb-2"><label class="form-label">Upload New Photo (optional)</label><input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif" class="form-control"></div>
    </div>

    <div class="mb-3">
      <button class="btn btn-primary">Update</button>
      <a class="btn btn-secondary" href="dashboard.php">Back</a>
    </div>
  </form>
</div>
</body>
</html>
