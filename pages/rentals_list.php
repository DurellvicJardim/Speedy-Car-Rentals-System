<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$search_text = '';
if (isset($_GET['search'])) {
  $search_text = trim($_GET['search']);
}

$rental_status_filter = '';
if (isset($_GET['rental_status'])) {
  $candidate = trim($_GET['rental_status']);
  if ($candidate === 'Rented' || $candidate === 'Returned' || $candidate === 'Overdue') {
    $rental_status_filter = $candidate;
  }
}

$sql_query = "SELECT rentals.*, cars.car_plate, cars.car_make, cars.car_model,
                     customers.first_name, customers.last_name
              FROM rentals
              JOIN cars ON rentals.car_id = cars.id
              JOIN customers ON rentals.customer_id = customers.id";

$conditions = array();
if ($search_text !== '') {
  $e = mysqli_real_escape_string($database_connection, $search_text);
  $conditions[] = "(cars.car_plate LIKE '%" . $e . "%'
                   OR cars.car_make LIKE '%" . $e . "%'
                   OR cars.car_model LIKE '%" . $e . "%'
                   OR customers.first_name LIKE '%" . $e . "%'
                   OR customers.last_name LIKE '%" . $e . "%')";
}
if ($rental_status_filter !== '') {
  $conditions[] = "rentals.rental_status = '" . mysqli_real_escape_string($database_connection, $rental_status_filter) . "'";
}
if (count($conditions) > 0) {
  $sql_query .= " WHERE " . implode(" AND ", $conditions);
}
$sql_query .= " ORDER BY rentals.id DESC";

$result_rentals = mysqli_query($database_connection, $sql_query);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Rentals</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-primary" href="rental_new.php">New Rental</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input class="form-control" name="search" placeholder="Search car or customer"
      value="<?php echo htmlspecialchars($search_text); ?>">
  </div>
  <div class="col-auto">
    <select class="form-select" name="rental_status">
      <option value="">All statuses</option>
      <option value="Rented" <?php echo ($rental_status_filter === 'Rented' ? 'selected' : ''); ?>>Rented</option>
      <option value="Returned" <?php echo ($rental_status_filter === 'Returned' ? 'selected' : ''); ?>>Returned</option>
      <option value="Overdue" <?php echo ($rental_status_filter === 'Overdue' ? 'selected' : ''); ?>>Overdue</option>
    </select>
  </div>
  <div class="col-auto"><button class="btn btn-secondary" type="submit">Filter</button></div>
</form>

<?php if (isset($_GET['message'])): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Car</th>
      <th>Customer</th>
      <th>Rented</th>
      <th>Expected</th>
      <th>Returned</th>
      <th>Status</th>
      <th>Cost</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($result_rentals)): ?>
      <tr>
        <td><?php echo (int) $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['car_plate'] . ' (' . $row['car_make'] . ' ' . $row['car_model'] . ')'); ?>
        </td>
        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['rent_date']); ?></td>
        <td><?php echo htmlspecialchars($row['expected_return_date']); ?></td>
        <td><?php echo htmlspecialchars($row['return_date']); ?></td>
        <td><?php echo htmlspecialchars($row['rental_status']); ?></td>
        <td><?php echo $row['cost'] === null ? '-' : 'R ' . number_format(num: (float) $row['cost'], decimals: 2); ?></td>
        <td class="text-nowrap">
          <?php
          $today_string = date('Y-m-d');
          $is_returned = ($row['rental_status'] === 'Returned');
          $is_overdue_candidate = (!$is_returned && empty($row['return_date']) && $row['expected_return_date'] < $today_string);
          ?>
          <?php if (!$is_returned): ?>
            <a class="btn btn-sm btn-outline-success"
              href="rental_return.php?rental_id=<?php echo (int) $row['id']; ?>">Mark
              Returned</a>
            <?php if ($is_overdue_candidate): ?>
              <a class="btn btn-sm btn-outline-warning" href="mark_overdue.php?rental_id=<?php echo (int) $row['id']; ?>">Mark
                Overdue</a>

            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>

