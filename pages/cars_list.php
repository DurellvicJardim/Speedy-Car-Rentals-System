<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$search_text = '';
if (isset($_GET['search'])) {
  $search_text = trim($_GET['search']);
}

$sql_query = "SELECT * FROM cars";
if ($search_text !== '') {
  $escaped = mysqli_real_escape_string($database_connection, $search_text);
  $sql_query .= " WHERE car_make LIKE '%" . $escaped . "%'
                    OR car_model LIKE '%" . $escaped . "%'
                    OR car_plate LIKE '%" . $escaped . "%'";
}
$sql_query .= " ORDER BY id DESC";

$result_cars = mysqli_query($database_connection, $sql_query);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Cars</h1>
  <a class="btn btn-primary" href="car_add.php">Add Car</a>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input class="form-control" name="search" placeholder="Search make, model, or plate"
      value="<?php echo htmlspecialchars($search_text); ?>">
  </div>
  <div class="col-auto"><button class="btn btn-secondary" type="submit">Search</button></div>
</form>

<?php if (isset($_GET['message'])): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<table class="table table-striped align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Make</th>
      <th>Model</th>
      <th>Year</th>
      <th>Plate</th>
      <th>Available</th>
      <th>Price/day (R)</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($result_cars)): ?>
      <tr>
        <td><?php echo (int) $row['id']; ?></td>
        <td>
          <?php if (!empty($row['image_path'])): ?>
            <img src="/car_rental_system/<?php echo htmlspecialchars($row['image_path']); ?>" alt="car"
              style="height:40px;">
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
        <td><?php echo htmlspecialchars($row['car_make']); ?></td>
        <td><?php echo htmlspecialchars($row['car_model']); ?></td>
        <td><?php echo htmlspecialchars($row['car_year']); ?></td>
        <td><?php echo htmlspecialchars($row['car_plate']); ?></td>
        <td><?php echo ((int) $row['availability'] === 1) ? 'Yes' : 'No'; ?></td>
        <td><?php echo number_format((float) $row['rental_price'], 2); ?></td>
        <td class="text-nowrap">
          <a class="btn btn-sm btn-outline-primary" href="car_edit.php?car_id=<?php echo (int) $row['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-outline-danger" href="car_delete.php?car_id=<?php echo (int) $row['id']; ?>">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>

