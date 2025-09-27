<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';

$rental_id = isset($_GET['rental_id']) ? (int) $_GET['rental_id'] : 0;
if ($rental_id <= 0) {
  header('Location: rentals_list.php?message=Invalid rental');
  exit;
}

$sql_update = "UPDATE rentals
               SET rental_status = 'Overdue'
               WHERE id = " . $rental_id . "
                 AND return_date IS NULL
                 AND expected_return_date < CURDATE()
                 AND rental_status <> 'Returned'";

mysqli_query($database_connection, $sql_update);

if (mysqli_affected_rows($database_connection) > 0) {
  header('Location: rentals_list.php?message=Marked overdue');
  exit;
} else {
  header('Location: rentals_list.php?message=Not overdue or already returned');
  exit;
}
