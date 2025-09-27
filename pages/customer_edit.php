<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$customer_id = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 0;
if ($customer_id <= 0) {
  echo '<div class="alert alert-danger">Invalid customer id.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$stmt0 = mysqli_prepare($database_connection, "SELECT * FROM customers WHERE id=?");
mysqli_stmt_bind_param($stmt0, "i", $customer_id);
mysqli_stmt_execute($stmt0);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt0));
mysqli_stmt_close($stmt0);
if (!$row) {
  echo '<div class="alert alert-danger">Customer not found.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$error_messages = array();
$first = $row['first_name'];
$last = $row['last_name'];
$email = $row['email'];
$phone = $row['phone'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['first_name'] ?? '');
  $last = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');

  if ($first === '')
    $error_messages[] = 'First name is required.';
  if ($last === '')
    $error_messages[] = 'Last name is required.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $error_messages[] = 'Valid email required.';

  if (count($error_messages) === 0) {
    $stmt = mysqli_prepare($database_connection, "UPDATE customers SET first_name=?, last_name=?, email=?, phone=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $first, $last, $email, $phone, $customer_id);
    $ok = mysqli_stmt_execute($stmt);
    $errno = mysqli_errno($database_connection);
    mysqli_stmt_close($stmt);
    if ($ok) {
      header('Location: customers_list.php?message=Customer updated');
      exit;
    }
    if ($errno == 1062)
      $error_messages[] = 'Email must be unique.';
    else
      $error_messages[] = 'Update failed.';
  }
}
?>
<h1>Edit Customer</h1>
<?php if ($error_messages): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($error_messages as $m) {
      echo '<li>' . htmlspecialchars($m) . '</li>';
    } ?></ul>
  </div><?php endif; ?>
<form method="post" class="row g-3" novalidate>
  <div class="col-md-6"><label class="form-label">First name</label><input class="form-control" name="first_name"
      value="<?php echo htmlspecialchars($first); ?>" required></div>
  <div class="col-md-6"><label class="form-label">Last name</label><input class="form-control" name="last_name"
      value="<?php echo htmlspecialchars($last); ?>" required></div>
  <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email"
      value="<?php echo htmlspecialchars($email); ?>" required></div>
  <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone"
      value="<?php echo htmlspecialchars($phone); ?>"></div>
  <div class="col-12"><button class="btn btn-primary" type="submit">Update</button><a class="btn btn-secondary ms-2"
      href="customers_list.php">Cancel</a></div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

