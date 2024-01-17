<?php
session_start();
if ($_SESSION['privilege_level'] != 'admin') {
  header('location: index.php');
  exit();
}
include("partials/db.php");

$query = "SELECT 
            COUNT(*) AS totalOrders, 
            COUNT(CASE WHEN status='pending' THEN 1 END) AS totalPendingOrders,
            COUNT(CASE WHEN status='delivered' THEN 1 END) AS totalDeliveredOrders,
            COUNT(CASE WHEN status='canceled' THEN 1 END) AS totalCanceledOrders
          FROM orders";

$result = $conn->query($query);

if ($result) {
  $row = $result->fetch_assoc();
  $totalOrders = $row['totalOrders'];
  $totalPendingOrders = $row['totalPendingOrders'];
  $totalDeliveredOrders = $row['totalDeliveredOrders'];
  $totalCanceledOrders = $row['totalCanceledOrders'];
} else {
  $msg = "Error: " . $conn->error;
}

$queryforRecentOrder = "SELECT * FROM orders"

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include("components/headerImports.php");
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Footwear - Ecommerce</title>
</head>

<body>
  <?php
  include('components/navbar.php');
  ?>

  <div class="dashboard-container">
    <div class="heading-section">
      <div class="text-side">
        <h1>Dashboard</h1>
        <p>Welcome to your Dashboard</p>
      </div>
      <div class="button-side">
        <button>
          <a class="page-button" href="addProduct.php">Add Product</a>
        </button>
      </div>
    </div>
    <div class="card-section">
      <a href="orderRecievedAdmin.php">
        <div class="dashbaord-card">
          <h2><?php echo $totalOrders ?></h2>
          <p>Total Order Recieved</p>
        </div>
      </a>

      <a href="orderPendingAdmin.php">
        <div class="dashbaord-card">
          <h2><?php echo $totalPendingOrders ?></h2>
          <p>Order Pending</p>
        </div>
      </a>
      <a href="orderDeliverAdmin.php">
        <div class="dashbaord-card">
          <h2><?php echo $totalDeliveredOrders ?></h2>
          <p>Order Delivered</p>
        </div>
      </a>
      <a href="orderCancelAdmin.php">
        <div class="dashbaord-card">
          <h2><?php echo $totalCanceledOrders ?></h2>
          <p>Order Canceled</p>
        </div>
      </a>
    </div>

    <div class="recent-section">
      <div class="heading">
        <h2>Recent Orders</h2>
        <a>View All</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>S.N</th>
            <th>Item Name</th>
            <th>Product ID</th>
            <th>Total Price</th>
            <th>Order Status</th>
            <th>Payment Status</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Shoe 1</td>
            <td>12345</td>
            <td>$49.99</td>
            <td>Shipped</td>
            <td>Paid</td>
            <td>View Details</td>
          </tr>
          <tr>
            <tr>
            <td>1</td>
            <td>Shoe 1</td>
            <td>12345</td>
            <td>$49.99</td>
            <td>Shipped</td>
            <td>Paid</td>
            <td>View Details</td>
          <!-- Add more rows for additional orders -->
        </tbody>
      </table>

    </div>
  </div>

</body>

</html>