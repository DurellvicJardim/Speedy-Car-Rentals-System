<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';

$rental_id = isset($_GET['rental_id']) ? (int) $_GET['rental_id'] : 0;
if ($rental_id <= 0) {
  header('Location: rentals_list.php?message=Invalid rental');
  exit;
}

$sql = "SELECT r.id, r.car_id, r.rent_date, c.rental_price
        FROM rentals r JOIN cars c ON r.car_id = c.id
        WHERE r.id = ? LIMIT 1";
$stmt = mysqli_prepare($database_connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $rental_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
  header('Location: rentals_list.php?message=Rental not found');
  exit;
}

$today = new DateTime('today');
$rent = new DateTime($row['rent_date']);
$days = (int) ceil(($today->getTimestamp() - $rent->getTimestamp()) / 86400);
if ($days < 1)
  $days = 1;
$cost = $days * (float) $row['rental_price'];

$stmt2 = mysqli_prepare($database_connection, "UPDATE rentals SET return_date = CURDATE(), rental_status='Returned', cost=? WHERE id=?");
mysqli_stmt_bind_param($stmt2, "di", $cost, $rental_id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

$stmt3 = mysqli_prepare($database_connection, "UPDATE cars SET availability = 1 WHERE id = ?");
mysqli_stmt_bind_param($stmt3, "i", $row['car_id']);
mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

header('Location: rentals_list.php?message=Rental returned (R ' . number_format($cost, 2) . ')');
exit;
