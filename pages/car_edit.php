<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$car_id = isset($_GET['car_id']) ? (int) $_GET['car_id'] : 0;
if ($car_id <= 0) {
  echo '<div class="alert alert-danger">Invalid car id.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$sql_one = "SELECT * FROM cars WHERE id = " . $car_id;
$result_one = mysqli_query($database_connection, $sql_one);
$current_car = mysqli_fetch_assoc($result_one);
if (!$current_car) {
  echo '<div class="alert alert-danger">Car not found.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$error_messages = array();
$car_make = $current_car['car_make'];
$car_model = $current_car['car_model'];
$car_year = (string) $current_car['car_year'];
$car_plate = $current_car['car_plate'];
$availability_checkbox = ((int) $current_car['availability'] === 1) ? 'on' : '';
$rental_price = (string) $current_car['rental_price'];
$image_path = $current_car['image_path'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $car_make = trim($_POST['car_make'] ?? '');
  $car_model = trim($_POST['car_model'] ?? '');
  $car_year = trim($_POST['car_year'] ?? '');
  $car_plate = trim($_POST['car_plate'] ?? '');
  $availability_checkbox = isset($_POST['availability']) ? 'on' : '';
  $rental_price = trim($_POST['rental_price'] ?? '');

  if ($car_make === '') {
    $error_messages[] = 'Make is required.';
  }
  if ($car_model === '') {
    $error_messages[] = 'Model is required.';
  }
  if ($car_year === '' || !ctype_digit($car_year)) {
    $error_messages[] = 'Year must be a whole number.';
  }
  if ($car_plate === '') {
    $error_messages[] = 'License plate is required.';
  }
  if ($rental_price === '' || !is_numeric($rental_price)) {
    $error_messages[] = 'Price must be a number.';
  }

  $new_image_path = $image_path;

  if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
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
          $new_image_path = $dest_rel;
        }
      }
    } else {
      $error_messages[] = 'Upload error. Please try again.';
    }
  }

  if (count($error_messages) === 0) {
    $availability_value = ($availability_checkbox === 'on') ? 1 : 0;

    $sql_update = "UPDATE cars SET
            car_make     = '" . mysqli_real_escape_string($database_connection, $car_make) . "',
            car_model    = '" . mysqli_real_escape_string($database_connection, $car_model) . "',
            car_year     = " . (int) $car_year . ",
            car_plate    = '" . mysqli_real_escape_string($database_connection, $car_plate) . "',
            availability = " . (int) $availability_value . ",
            rental_price = " . (float) $rental_price . ",
            image_path   = '" . mysqli_real_escape_string($database_connection, $new_image_path) . "'
            WHERE id = " . $car_id;

    $update_ok = mysqli_query($database_connection, $sql_update);

    if ($update_ok) {
      header('Location: cars_list.php?message=Car updated');
      exit;
    } else {
      if (mysqli_errno($database_connection) == 1062) {
        $error_messages[] = 'License plate must be unique.';
      } else {
        $error_messages[] = 'Update failed.';
      }
    }
  }
}
?>
<h1>Edit Car</h1>

<?php if (count($error_messages) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($error_messages as $msg): ?>
        <li><?php echo htmlspecialchars($msg); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="row g-3" enctype="multipart/form-data">
  <div class="col-md-6">
    <label class="form-label">Make</label>
    <input class="form-control" name="car_make" value="<?php echo htmlspecialchars($car_make); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Model</label>
    <input class="form-control" name="car_model" value="<?php echo htmlspecialchars($car_model); ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">Year</label>
    <input class="form-control" name="car_year" value="<?php echo htmlspecialchars($car_year); ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">License plate</label>
    <input class="form-control" name="car_plate" value="<?php echo htmlspecialchars($car_plate); ?>">
  </div>
  <div class="col-md-4 form-check mt-4">
    <input class="form-check-input" type="checkbox" name="availability" id="availability" <?php echo ($availability_checkbox === 'on') ? 'checked' : ''; ?>>
    <label class="form-check-label" for="availability">Available</label>
  </div>
  <div class="col-md-4">
    <label class="form-label">Price per day (R)</label>
    <input class="form-control" name="rental_price" value="<?php echo htmlspecialchars($rental_price); ?>">
  </div>
  <div class="col-md-8">
    <label class="form-label">Replace car image (optional)</label>
    <input class="form-control" type="file" name="car_image" accept=".jpg,.jpeg,.png,.gif">
    <?php if (!empty($image_path)): ?>
      <div class="form-text">Current: <?php echo htmlspecialchars($image_path); ?></div>
      <img src="/car_rental_system/<?php echo htmlspecialchars($image_path); ?>" alt="car"
        style="height:60px;margin-top:6px;">
    <?php endif; ?>
  </div>
  <div class="col-12">
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn btn-secondary" href="cars_list.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

