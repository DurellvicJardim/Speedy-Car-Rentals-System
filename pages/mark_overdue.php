<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';

$rental_id = isset($_GET['rental_id']) ? (int) $_GET['rental_id'] : 0;
if ($rental_id <= 0) {
  header('Location: rentals_list.php?message=Invalid rental');
  exit;
}

$stmt = mysqli_prepare($database_connection, "UPDATE rentals
  SET rental_status = 'Overdue'
  WHERE id = ?
    AND return_date IS NULL
    AND expected_return_date < CURDATE()
    AND rental_status <> 'Returned'");
mysqli_stmt_bind_param($stmt, "i", $rental_id);
mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

header('Location: rentals_list.php?message=' . ($affected > 0 ? 'Marked overdue' : 'Not overdue or already returned'));
exit;
