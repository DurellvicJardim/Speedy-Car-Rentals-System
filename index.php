<?php
require __DIR__ . '/includes/require_login.php';
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/header.php';

function get_count($database_connection, $sql_text) {
  $res = mysqli_query($database_connection, $sql_text);
  $row = mysqli_fetch_row($res);
  return (int)$row[0];
}
$total_cars = get_count($database_connection, "SELECT COUNT(*) FROM cars");
$available_cars = get_count($database_connection, "SELECT COUNT(*) FROM cars WHERE availability=1");
$total_customers = get_count($database_connection, "SELECT COUNT(*) FROM customers");
$rented_cnt = get_count($database_connection, "SELECT COUNT(*) FROM rentals WHERE rental_status='Rented'");
$returned_cnt = get_count($database_connection, "SELECT COUNT(*) FROM rentals WHERE rental_status='Returned'");
$overdue_cnt = get_count($database_connection, "SELECT COUNT(*) FROM rentals WHERE rental_status='Overdue'");
?>
<h1 class="mb-4">Dashboard</h1>
<div class="row g-3">
  <div class="col-md-4"><div class="card"><div class="card-body">
    <h5>Total cars</h5><p class="display-6"><?php echo $total_cars; ?></p>
    <small>Available: <?php echo $available_cars; ?></small>
  </div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body">
    <h5>Customers</h5><p class="display-6"><?php echo $total_customers; ?></p>
  </div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body">
    <h5>Rentals</h5>
    <div>Rented: <?php echo $rented_cnt; ?></div>
    <div>Returned: <?php echo $returned_cnt; ?></div>
    <div>Overdue: <?php echo $overdue_cnt; ?></div>
  </div></div></div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
