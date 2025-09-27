<?php
require __DIR__ . '/../includes/require_login.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/header.php';

$car_id = isset($_GET['car_id']) ? (int) $_GET['car_id'] : 0;
if ($car_id <= 0) {
    echo '<div class="alert alert-danger">Invalid car id.</div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$sql_one = "SELECT * FROM cars WHERE id = " . $car_id;
$result_one = mysqli_query($database_connection, $sql_one);
$current_car = mysqli_fetch_assoc($result_one);
if (!$current_car) {
    echo '<div class="alert alert-danger">Car not found.</div>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql_delete = "DELETE FROM cars WHERE id = " . $car_id;
    $delete_ok = mysqli_query($database_connection, $sql_delete);
    if ($delete_ok) {
        header('Location: cars_list.php?message=Car deleted');
        exit;
    } else {
        echo '<div class="alert alert-danger">Delete failed. This car may be used by rentals.</div>';
    }
}
?>
<h1>Delete Car</h1>
<div class="alert alert-warning">
    Delete car:
    <strong><?php echo htmlspecialchars($current_car['car_make'] . ' ' . $current_car['car_model'] . ' (' . $current_car['car_plate'] . ')'); ?></strong>?
</div>
<form method="post">
    <button class="btn btn-danger" type="submit">Yes, delete</button>
    <a class="btn btn-secondary" href="cars_list.php">Cancel</a>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

