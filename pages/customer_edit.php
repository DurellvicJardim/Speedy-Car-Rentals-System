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

$sql_one = "SELECT * FROM customers WHERE id = " . $customer_id;
$result_one = mysqli_query($database_connection, $sql_one);
$current_customer = mysqli_fetch_assoc($result_one);
if (!$current_customer) {
  echo '<div class="alert alert-danger">Customer not found.</div>';
  require __DIR__ . '/../includes/footer.php';
  exit;
}

$error_messages = array();
$first_name = $current_customer['first_name'];
$last_name = $current_customer['last_name'];
$email = $current_customer['email'];
$phone = $current_customer['phone'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = trim($_POST['first_name'] ?? '');
  $last_name = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');

  if ($first_name === '') {
    $error_messages[] = 'First name is required.';
  }
  if ($last_name === '') {
    $error_messages[] = 'Last name is required.';
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_messages[] = 'Valid email is required.';
  }
  if ($phone !== '' && strlen($phone) > 12) {
    $error_messages[] = 'Phone must be 12 characters or fewer.';
  }

  if (count($error_messages) === 0) {
    $sql_update = "UPDATE customers SET
            first_name = '" . mysqli_real_escape_string($database_connection, $first_name) . "',
            last_name  = '" . mysqli_real_escape_string($database_connection, $last_name) . "',
            email      = '" . mysqli_real_escape_string($database_connection, $email) . "',
            phone      = '" . mysqli_real_escape_string($database_connection, $phone) . "'
            WHERE id = " . $customer_id;

    $update_ok = mysqli_query($database_connection, $sql_update);

    if ($update_ok) {
      header('Location: customers_list.php?message=Customer updated');
      exit;
    } else {
      if (mysqli_errno($database_connection) == 1062) {
        $error_messages[] = 'Email must be unique.';
      } else {
        $error_messages[] = 'Update failed.';
      }
    }
  }
}
?>
<h1>Edit Customer</h1>

<?php if (count($error_messages) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($error_messages as $msg): ?>
        <li><?php echo htmlspecialchars($msg); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="row g-3">
  <div class="col-md-6">
    <label class="form-label">First name</label>
    <input class="form-control" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Last name</label>
    <input class="form-control" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Phone</label>
    <input class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
  </div>
  <div class="col-12">
    <button class="btn btn-primary" type="submit">Update</button>
    <a class="btn btn-secondary" href="customers_list.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

