<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$error_messages = array();
$car_make = '';
$car_model = '';
$car_year = '';
$car_plate = '';
$availability_checkbox = 'on';
$rental_price = '';
$image_path = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $car_make = trim($_POST['car_make'] ?? '');
  $car_model = trim($_POST['car_model'] ?? '');
  $car_year = trim($_POST['car_year'] ?? '');
  $car_plate = trim($_POST['car_plate'] ?? '');
  $availability_checkbox = isset($_POST['availability']) ? 'on' : '';
  $rental_price = trim($_POST['rental_price'] ?? '');

  if ($car_make === '')
    $error_messages[] = 'Make is required.';
  if ($car_model === '')
    $error_messages[] = 'Model is required.';
  if ($car_year === '' || !ctype_digit($car_year))
    $error_messages[] = 'Year must be a whole number.';
  if ($car_plate === '')
    $error_messages[] = 'License plate is required.';
  if ($rental_price === '' || !is_numeric($rental_price))
    $error_messages[] = 'Price must be a number.';

  if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['car_image']['tmp_name'];
    $name = $_FILES['car_image']['name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
      $error_messages[] = 'Image must be JPG, PNG, or GIF.';
    } else {
      $new_name = 'car_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
      $dest_rel = 'images/' . $new_name;
      $dest_abs = __DIR__ . '/../' . $dest_rel;
      if (!move_uploaded_file($tmp, $dest_abs)) {
        $error_messages[] = 'Could not save the uploaded image.';
      } else {
        $image_path = $dest_rel;
      }
    }
  }

  if (count($error_messages) === 0) {
    $availability_value = ($availability_checkbox === 'on') ? 1 : 0;
    $stmt = mysqli_prepare(
      $database_connection,
      "INSERT INTO cars (car_make, car_model, car_year, car_plate, availability, rental_price, image_path)
       VALUES (?,?,?,?,?,?,?)"
    );
    mysqli_stmt_bind_param(
      $stmt,
      "ssissds",
      $car_make,
      $car_model,
      $car_year,
      $car_plate,
      $availability_value,
      $rental_price,
      $image_path
    );
    $ok = mysqli_stmt_execute($stmt);
    $errno = mysqli_errno($database_connection);
    mysqli_stmt_close($stmt);

    if ($ok) {
      header('Location: cars_list.php?message=Car added');
      exit;
    }
    if ($errno == 1062)
      $error_messages[] = 'License plate must be unique.';
    else
      $error_messages[] = 'Insert failed.';
  }
}
?>
<h1>Add Car</h1>
<?php if (count($error_messages)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($error_messages as $m) {
      echo '<li>' . htmlspecialchars($m) . '</li>';
    } ?></ul>
  </div><?php endif; ?>
<form method="post" class="row g-3" enctype="multipart/form-data" novalidate>
  <div class="col-md-6"><label class="form-label">Make</label><input class="form-control" name="car_make"
      value="<?php echo htmlspecialchars($car_make); ?>" required></div>
  <div class="col-md-6"><label class="form-label">Model</label><input class="form-control" name="car_model"
      value="<?php echo htmlspecialchars($car_model); ?>" required></div>
  <div class="col-md-4"><label class="form-label">Year</label><input class="form-control" type="number" name="car_year"
      value="<?php echo htmlspecialchars($car_year); ?>" required min="1900" max="2099" step="1"></div>
  <div class="col-md-4"><label class="form-label">License plate</label><input class="form-control" name="car_plate"
      value="<?php echo htmlspecialchars($car_plate); ?>" required></div>
  <div class="col-md-4 form-check mt-4">
    <input class="form-check-input" type="checkbox" name="availability" id="availability" <?php echo ($availability_checkbox === 'on') ? 'checked' : ''; ?>>
    <label class="form-check-label" for="availability">Available</label>
  </div>
  <div class="col-md-4"><label class="form-label">Price per day (R)</label><input class="form-control" type="number"
      name="rental_price" value="<?php echo htmlspecialchars($rental_price); ?>" required min="0" step="0.01"></div>
  <div class="col-md-8"><label class="form-label">Car image (JPG/PNG/GIF)</label><input class="form-control" type="file"
      name="car_image" accept=".jpg,.jpeg,.png,.gif"></div>
  <div class="col-12"><button class="btn btn-primary" type="submit">Save</button><a class="btn btn-secondary ms-2"
      href="cars_list.php">Cancel</a></div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

