<?php
  require __DIR__ . '/includes/db.php';

  $employee_text = 'employee1'; //put the employee name here
  $email_text = 'employee1@email.com'; //put the employee email here
  $password_plain = 'employee123'; //put the employee password here
  $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

  $employee_esc = mysqli_real_escape_string($database_connection, $employee_text);
  $email_esc = mysqli_real_escape_string($database_connection, $email_text);
  $hash_esc = mysqli_real_escape_string($database_connection, $password_hash);

  $sql = "INSERT INTO users (employee, email, password_hash) VALUES ('".$employee_esc."', '".$email_esc."', '".$hash_esc."')";
  $ok = mysqli_query($database_connection, $sql);

  if ($ok) {
    echo "Emplyee Account Created!";
  } else {
    echo "FAILED or already exists.";
  }
