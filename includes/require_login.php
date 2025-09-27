<?php
require __DIR__ . '/session.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: /car_rental_system/pages/login.php');
  exit;
}
