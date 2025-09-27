<?php require __DIR__ . '/session.php'; ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/car_rental_system/css/styles.css" rel="stylesheet">
</head>

<body class="bg-light">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="/car_rental_system/">
        <img src="/car_rental_system/images/mini-car.gif" alt="logo" class="logo me-2">
        <span>Speedy Wheels Car Rentals</span>
      </a>

      <div class="d-flex gap-3">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a class="nav-link text-white" href="/car_rental_system/pages/cars_list.php">Cars</a>
          <a class="nav-link text-white" href="/car_rental_system/pages/customers_list.php">Customers</a>
          <a class="nav-link text-white" href="/car_rental_system/pages/rentals_list.php">Rentals</a>
          <a class="nav-link text-white" href="/car_rental_system/pages/logout.php">Logout</a>
        <?php else: ?>
          <a class="nav-link text-white" href="/car_rental_system/pages/login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <main class="container py-4">
