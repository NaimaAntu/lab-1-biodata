<?php
// add_biodata.php
include 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$fields = [
    'full_name','dob','age','gender','religion','caste','nationality','phone','email','address',
    'height','weight','blood_group','complexion','education','occupation','annual_income','work_location',
    'father_name','father_occupation','mother_name','mother_occupation','siblings','preferred_age_min','preferred_age_max',
    'preferred_education','preferred_location','hobbies','about_me','expectations'
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // gather inputs (trim strings)
    $data = [];
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');

    // Photo handling
    $photoName = null;
    if (!empty($_FILES['photo']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $tmp = $_FILES['photo']['tmp_name'];
        $orig = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $errors[] = "Invalid photo format. Allowed: jpg,jpeg,png,gif.";
        elseif ($_FILES['photo']['size'] > $maxSize) $errors[] = "Photo too large (max 2MB).";
        else {
            // unique filename
            $photoName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            if (!move_uploaded_file($tmp, UPLOAD_DIR . $photoName)) $errors[] = "Failed to move uploaded file.";
        }
    } else {
        $errors[] = "Photo is required.";
    }

    if (empty($data['full_name'])) $errors[] = "Full name is required.";

    if (empty($errors)) {
        // Build SQL
        $columns = implode(",", $fields) . ", photo, user_id";
        $placeholders = implode(",", array_fill(0, count($fields), "?")) . ", ?, ?";
        $sql = "INSERT INTO biodata ($columns) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);

        // types: treat all as strings except user_id
        $types = str_repeat("s", count($fields)) . "si";
        $params = array_merge(array_map(function($k) use ($data){ return $data[$k]; }, $fields), [$photoName, $_SESSION['user_id']]);

        // bind dynamically
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "DB Error: " . $stmt->error;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Bio-data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h3>Add Matrimonial Bio-data</h3>
  <?php if($errors): foreach($errors as $er): ?><div class="alert alert-danger"><?= e($er) ?></div><?php endforeach; endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="card p-3 mb-3">
      <h5>Personal Information</h5>
      <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Full Name *</label><input name="full_name" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">DOB</label><input type="date" name="dob" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Age</label><input type="number" name="age" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Gender</label>
            <select name="gender" class="form-control">
              <option value="">Select</option><option>Male</option><option>Female</option>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">Religion</label><input name="religion" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Caste</label><input name="caste" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Nationality</label><input name="nationality" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Contact</h5>
      <div class="row g-2">
        <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
        <div class="col-md-12"><label class="form-label">Address</label><textarea name="address" class="form-control"></textarea></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Physical Details</h5>
      <div class="row g-2">
        <div class="col-md-3"><label class="form-label">Height (cm)</label><input step="0.1" name="height" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Weight (kg)</label><input step="0.01" name="weight" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Blood Group</label><input name="blood_group" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Complexion</label><input name="complexion" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Education & Career</h5>
      <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Education</label><input name="education" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Occupation</label><input name="occupation" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Annual Income</label><input name="annual_income" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Work Location</label><input name="work_location" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Family Background</h5>
      <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Father's Name</label><input name="father_name" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Father's Occupation</label><input name="father_occupation" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Mother's Name</label><input name="mother_name" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Mother's Occupation</label><input name="mother_occupation" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Siblings</label><input type="number" name="siblings" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Partner Preferences</h5>
      <div class="row g-2">
        <div class="col-md-3"><label class="form-label">Preferred Age Min</label><input type="number" name="preferred_age_min" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Preferred Age Max</label><input type="number" name="preferred_age_max" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Preferred Education</label><input name="preferred_education" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Preferred Location</label><input name="preferred_location" class="form-control"></div>
      </div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Other Details</h5>
      <div class="mb-2"><label class="form-label">Hobbies</label><textarea name="hobbies" class="form-control"></textarea></div>
      <div class="mb-2"><label class="form-label">About Me</label><textarea name="about_me" class="form-control"></textarea></div>
      <div class="mb-2"><label class="form-label">Expectations</label><textarea name="expectations" class="form-control"></textarea></div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Photo</h5>
      <div class="mb-2"><input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif" class="form-control" required></div>
      <small class="text-muted">Max 2MB. JPG/PNG/GIF allowed.</small>
    </div>

    <div class="mb-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-secondary" href="dashboard.php">Back</a>
    </div>
  </form>
</div>
</body>
</html>
