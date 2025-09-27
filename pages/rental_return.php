<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$rental_id = isset($_GET['rental_id']) ? (int) $_GET['rental_id'] : 0;
if ($rental_id <= 0) {
  echo '<div class="alert alert-danger">Invalid rental id.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$sql_one = "SELECT rentals.*, cars.rental_price, cars.id AS car_row_id
            FROM rentals
            JOIN cars ON rentals.car_id = cars.id
            WHERE rentals.id = " . $rental_id;
$result_one = mysqli_query($database_connection, $sql_one);
$current_rental = mysqli_fetch_assoc($result_one);
if (!$current_rental) {
  echo '<div class="alert alert-danger">Rental not found.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$error_messages = array();
$calculated_days = 1;
$calculated_cost = 0.00;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rent_date_string = $current_rental['rent_date'];
  $today_string = date('Y-m-d');

  $rent_date_time = strtotime($rent_date_string);
  $today_time = strtotime($today_string);
  $difference_seconds = $today_time - $rent_date_time;
  $days = (int) ceil($difference_seconds / 86400);
  if ($days < 1) {
    $days = 1;
  }

  $price_per_day = (float) $current_rental['rental_price'];
  $cost_total = $days * $price_per_day;

  $sql_update_rental = "UPDATE rentals SET
        return_date = CURDATE(),
        rental_status = 'Returned',
        cost = " . $cost_total . "
        WHERE id = " . $rental_id;

  $ok1 = mysqli_query($database_connection, $sql_update_rental);
  $ok2 = mysqli_query($database_connection, "UPDATE cars SET availability = 1 WHERE id = " . (int) $current_rental['car_row_id']);

  if ($ok1 && $ok2) {
    header('Location: rentals_list.php?message=Rental returned');
    exit;
  } else {
    $error_messages[] = 'Could not mark as returned.';
  }
} else {
  $rent_date_string = $current_rental['rent_date'];
  $today_string = date('Y-m-d');

  $rent_date_time = strtotime($rent_date_string);
  $today_time = strtotime($today_string);
  $difference_seconds = $today_time - $rent_date_time;
  $days = (int) ceil($difference_seconds / 86400);
  if ($days < 1) {
    $days = 1;
  }
  $calculated_days = $days;
  $calculated_cost = $calculated_days * (float) $current_rental['rental_price'];
}
?>
<h1>Return Rental</h1>

<?php if (count($error_messages) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($error_messages as $msg): ?>
        <li><?php echo htmlspecialchars($msg); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<p>Return this rental now? Days charged: <strong><?php echo $calculated_days; ?></strong>.
  Estimated cost: <strong>R <?php echo number_format($calculated_cost, 2); ?></strong></p>

<form method="post">
  <button class="btn btn-success" type="submit">Confirm Return</button>
  <a class="btn btn-secondary" href="rentals_list.php">Cancel</a>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

