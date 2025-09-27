<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$error_messages = array();
$info_message = '';
$step = 'email';
$found_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'find') {
  $input_email = trim($_POST['email'] ?? '');
  if ($input_email === '' || !filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
    $error_messages[] = 'Please enter a valid email address.';
  } else {
    $stmt = mysqli_prepare($database_connection, "SELECT id, email FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $input_email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($row) {
      $step = 'reset';
      $found_email = $row['email'];
      $info_message = 'Email found. Please enter a new password.';
    } else {
      $error_messages[] = 'This email is not linked to any current employees.';
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset') {
  $found_email = trim($_POST['email_confirm'] ?? '');
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($found_email === '' || !filter_var($found_email, FILTER_VALIDATE_EMAIL)) {
    $error_messages[] = 'Invalid email provided.';
    $step = 'email';
  }
  if ($new_password === '' || strlen($new_password) < 6) {
    $error_messages[] = 'Password must be at least 6 characters.';
    $step = 'reset';
  }
  if ($new_password !== $confirm_password) {
    $error_messages[] = 'Passwords do not match.';
    $step = 'reset';
  }

  if (count($error_messages) === 0) {
    $stmt = mysqli_prepare($database_connection, "SELECT id FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $found_email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$user) {
      $error_messages[] = 'This email is not linked to any current employees.';
      $step = 'email';
    } else {
      $hash = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt2 = mysqli_prepare($database_connection, "UPDATE users SET password_hash = ? WHERE id = ?");
      mysqli_stmt_bind_param($stmt2, "si", $hash, $user['id']);
      mysqli_stmt_execute($stmt2);
      mysqli_stmt_close($stmt2);
      header('Location: login.php?message=Password has been reset. Please log in.');
      exit;
    }
  }
}
?>
<h1>Forgot Password</h1>

<?php if ($info_message !== ''): ?>
  <div class="alert alert-info"><?php echo htmlspecialchars($info_message); ?></div><?php endif; ?>
<?php if (count($error_messages) > 0): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($error_messages as $m): ?>
        <li><?php echo htmlspecialchars($m); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($step === 'email'): ?>
  <form method="post" class="row g-3" novalidate>
    <input type="hidden" name="action" value="find">
    <div class="col-md-6">
      <label class="form-label">Enter your registered email</label>
      <input class="form-control" type="email" name="email" placeholder="name@example.com" required>
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit">Continue</button>
      <a class="btn btn-secondary" href="login.php">Back to Login</a>
    </div>
  </form>
<?php else: ?>
  <form method="post" class="row g-3" novalidate>
    <input type="hidden" name="action" value="reset">
    <input type="hidden" name="email_confirm" value="<?php echo htmlspecialchars($found_email); ?>">
    <div class="col-md-6">
      <label class="form-label">New password</label>
      <input class="form-control" type="password" name="new_password" required minlength="6">
    </div>
    <div class="col-md-6">
      <label class="form-label">Confirm new password</label>
      <input class="form-control" type="password" name="confirm_password" required minlength="6">
    </div>
    <div class="col-12">
      <button class="btn btn-success" type="submit">Set New Password</button>
      <a class="btn btn-secondary" href="login.php">Cancel</a>
    </div>
  </form>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

