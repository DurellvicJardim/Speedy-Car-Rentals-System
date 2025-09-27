<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';

$car_id = isset($_GET['car_id']) ? (int) $_GET['car_id'] : 0;
if ($car_id > 0) {
    $stmt = mysqli_prepare($database_connection, "DELETE FROM cars WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
header('Location: cars_list.php?message=Car deleted');
exit;
