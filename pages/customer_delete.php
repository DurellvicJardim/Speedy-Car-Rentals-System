<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';

$customer_id = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 0;
if ($customer_id > 0) {
    $stmt = mysqli_prepare($database_connection, "DELETE FROM customers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
header('Location: customers_list.php?message=Customer deleted');
exit;
