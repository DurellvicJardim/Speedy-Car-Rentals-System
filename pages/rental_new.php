<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$error_messages = array();
$car_id = '0';
$customer_id = '0';
$rent_date = '';
$expected_return_date = '';

$result_cars = mysqli_query($database_connection, "SELECT id, car_plate, car_make, car_model FROM cars WHERE availability = 1 ORDER BY id DESC");
$result_customers = mysqli_query($database_connection, "SELECT id, first_name, last_name FROM customers ORDER BY id DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $car_id = isset($_POST['car_id']) ? (int) $_POST['car_id'] : 0;
  $customer_id = isset($_POST['customer_id']) ? (int) $_POST['customer_id'] : 0;
  $rent_date = trim($_POST['rent_date'] ?? '');
  $expected_return_date = trim($_POST['expected_return_date'] ?? '');

  if ($car_id <= 0)
    $error_messages[] = 'Please choose a car.';
  if ($customer_id <= 0)
    $error_messages[] = 'Please choose a customer.';
  if ($rent_date === '')
    $error_messages[] = 'Please choose a start date.';
  if ($expected_return_date === '')
    $error_messages[] = 'Please choose an expected return date.';
  if ($rent_date !== '' && $expected_return_date !== '' && $rent_date > $expected_return_date)
    $error_messages[] = 'Expected return must be same day or after the start date.';

  if (count($error_messages) === 0) {
    $stmt = mysqli_prepare(
      $database_connection,
      "INSERT INTO rentals (car_id, customer_id, rent_date, expected_return_date, rental_status) VALUES (?,?,?,?, 'Rented')"
    );
    mysqli_stmt_bind_param($stmt, "iiss", $car_id, $customer_id, $rent_date, $expected_return_date);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
      $stmt2 = mysqli_prepare($database_connection, "UPDATE cars SET availability = 0 WHERE id = ?");
      mysqli_stmt_bind_param($stmt2, "i", $car_id);
      mysqli_stmt_execute($stmt2);
      mysqli_stmt_close($stmt2);
      header('Location: rentals_list.php?message=Rental started');
      exit;
    } else {
      $error_messages[] = 'Could not create rental.';
    }
  }
}
?>
<h1>New Rental</h1>
<?php if ($error_messages): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($error_messages as $m) {
      echo '<li>' . htmlspecialchars($m) . '</li>';
    } ?></ul>
  </div><?php endif; ?>

<form method="post" class="row gy-3" novalidate>
  <div class="col-md-6">
    <label class="form-label">Car (available)</label>
    <select class="form-select" name="car_id" required>
      <option value="0">Select a car</option>
      <?php mysqli_data_seek($result_cars, 0);
      while ($c = mysqli_fetch_assoc($result_cars)): ?>
        <option value="<?php echo (int) $c['id']; ?>" <?php echo ($car_id == (int) $c['id'] ? 'selected' : ''); ?>>
          <?php echo htmlspecialchars($c['car_plate'] . ' - ' . $c['car_make'] . ' ' . $c['car_model']); ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Customer</label>
    <select class="form-select" name="customer_id" required>
      <option value="0">Select a customer</option>
      <?php mysqli_data_seek($result_customers, 0);
      while ($u = mysqli_fetch_assoc($result_customers)): ?>
        <option value="<?php echo (int) $u['id']; ?>" <?php echo ($customer_id == (int) $u['id'] ? 'selected' : ''); ?>>
          <?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Start date</label>
    <input class="form-control" type="date" name="rent_date" value="<?php echo htmlspecialchars($rent_date); ?>"
      required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Expected return date</label>
    <input class="form-control" type="date" name="expected_return_date"
      value="<?php echo htmlspecialchars($expected_return_date); ?>" required>
  </div>

  <div class="col-12">
    <button class="btn btn-primary" type="submit">Start Rental</button>
    <a class="btn btn-secondary" href="rentals_list.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

