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
      <div class="dashbaord-card">
        <h2>400</h2>
        <p>Order Recieved</p>
      </div>
      <div class="dashbaord-card">
        <h2>400</h2>
        <p>Order Pending</p>
      </div>
    </div>

    <div class="recent-section">
      <div class="heading">
        <h2>Recent Orders</h2>
        <a>View All</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>ITEM</th>
            <th>Product ID</th>
            <th>PRICE</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <!-- Replace the sample data with actual data from your database -->
          <tr>
            <td>Shoe 1</td>
            <td>12345</td>
            <td>$49.99</td>
            <td>Shipped</td>
            <td>View Details</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Shoe 2</td>
            <td>1</td>
            <td>$50.00</td>
            <td>2023-01-02 15:45:00</td>
          </tr>
          <!-- Add more rows for additional orders -->
        </tbody>
      </table>

    </div>
  </div>

</body>

</html>