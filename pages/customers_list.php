<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$search_text = '';
if (isset($_GET['search'])) {
  $search_text = trim($_GET['search']);
}

$sql_query = "SELECT * FROM customers";
if ($search_text !== '') {
  $escaped = mysqli_real_escape_string($database_connection, $search_text);
  $sql_query .= " WHERE first_name LIKE '%" . $escaped . "%'
                    OR last_name LIKE '%" . $escaped . "%'
                    OR email LIKE '%" . $escaped . "%'
                    OR phone LIKE '%" . $escaped . "%'";
}
$sql_query .= " ORDER BY id DESC";

$result_customers = mysqli_query($database_connection, $sql_query);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Customers</h1>
  <a class="btn btn-primary" href="customer_add.php">Add Customer</a>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input class="form-control" name="search" placeholder="Search name, email, or phone"
      value="<?php echo htmlspecialchars($search_text); ?>">
  </div>
  <div class="col-auto"><button class="btn btn-secondary" type="submit">Search</button></div>
</form>

<?php if (isset($_GET['message'])): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>First</th>
      <th>Last</th>
      <th>Email</th>
      <th>Phone</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($result_customers)): ?>
      <tr>
        <td><?php echo (int) $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['phone']); ?></td>
        <td class="text-nowrap">
          <a class="btn btn-sm btn-outline-primary"
            href="customer_edit.php?customer_id=<?php echo (int) $row['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-outline-danger"
            href="customer_delete.php?customer_id=<?php echo (int) $row['id']; ?>">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>

