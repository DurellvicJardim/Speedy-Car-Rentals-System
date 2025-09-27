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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql_delete = "DELETE FROM customers WHERE id = " . $customer_id;
    $delete_ok = mysqli_query($database_connection, $sql_delete);
    if ($delete_ok) {
        header('Location: customers_list.php?message=Customer deleted');
        exit;
    } else {
        echo '<div class="alert alert-danger">Delete failed. This customer may be used by rentals.</div>';
    }
}
?>
<h1>Delete Customer</h1>
<div class="alert alert-warning">
    Delete customer:
    <strong><?php echo htmlspecialchars($current_customer['first_name'] . ' ' . $current_customer['last_name'] . ' (' . $current_customer['email'] . ')'); ?></strong>?
</div>
<form method="post">
    <button class="btn btn-danger" type="submit">Yes, delete</button>
    <a class="btn btn-secondary" href="customers_list.php">Cancel</a>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

