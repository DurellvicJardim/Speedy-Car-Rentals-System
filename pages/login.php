<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/header.php';

$error_messages = array();
$employee_text = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $employee_text = trim($_POST['employee'] ?? '');
  $password_text = $_POST['password'] ?? '';

  if ($employee_text === '') {
    $error_messages[] = 'Employee is required.';
  }
  if ($password_text === '') {
    $error_messages[] = 'Password is required.';
  }

  if (count($error_messages) === 0) {
    $stmt = mysqli_prepare($database_connection, "SELECT id, employee, password_hash FROM users WHERE employee = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $employee_text);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_row = mysqli_fetch_assoc($result);

    if ($user_row && password_verify($password_text, $user_row['password_hash'])) {
      $_SESSION['user_id'] = (int) $user_row['id'];
      $_SESSION['employee'] = $user_row['employee'];
      header('Location: /car_rental_system/');
      exit;
    } else {
      $error_messages[] = 'Invalid employee or password.';
    }
    mysqli_stmt_close($stmt);
  }
}
?>
<h1>Login</h1>
<?php if (count($error_messages) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($error_messages as $m): ?>
        <li><?php echo htmlspecialchars($m); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="row g-3" autocomplete="off" novalidate>
  <div class="col-md-6">
    <label class="form-label">Employee</label>
    <input class="form-control" name="employee" value="<?php echo htmlspecialchars($employee_text); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Password</label>
    <input class="form-control" type="password" name="password" required minlength="6">
  </div>
  <div class="col-12">
    <button class="btn btn-primary" type="submit">Login</button>
  </div>
  <p class="mt-3"><a href="forgot_password.php">Forgot your password?</a></p>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

