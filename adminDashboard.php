<?php
session_start();
if ($_SESSION['privilege_level'] != 'admin') {
  header('location: index.php');
  exit();
}
include("partials/db.php");

//query to get number of order recieved, pending, deliver, cancel
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

$recordsPerPage = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;
//query for recent orders
$queryforRecentOrder = "
SELECT 
orders.order_id, 
MAX(orders.user_id) AS user_id, 
MAX(orders.order_date) AS order_date, 
MAX(orders.total_amount) AS total_amount, 
MAX(orders.status) AS status, 
MAX(orders.shipping_address) AS shipping_address, 
MAX(orders.payment_status) AS payment_status, 
MAX(orders.token) AS token, 
GROUP_CONCAT(order_items.order_item_id) AS order_item_ids, 
GROUP_CONCAT(order_items.size) AS sizes, 
GROUP_CONCAT(products_table.id) AS product_ids, 
GROUP_CONCAT(SUBSTRING(products_table.name, 1, 15)) AS product_names, 
GROUP_CONCAT(order_items.quantity) AS quantities, 
GROUP_CONCAT(order_items.price) AS prices 
FROM orders 
INNER JOIN 
order_items ON orders.order_id = order_items.order_id 
INNER JOIN products_table 
ON order_items.product_id = products_table.id 
GROUP BY orders.order_id
ORDER BY 
    MAX(orders.order_date) DESC
LIMIT 
$offset, $recordsPerPage
;
";
$resultforRecentOrder = $conn->query($queryforRecentOrder);

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
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>Order Id</th>
            <th>Item Name</th>
            <th>Product ID</th>
            <th>Total Price</th>
            <th>Shipping Address</th>
            <th>Payment Status</th>
            <th>Token</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $status = '';
          if ($resultforRecentOrder) {
            while ($row = $resultforRecentOrder->fetch_assoc()) {
              if ($row['payment_status'] === 'paid') {
                $status = 'paid via khalti';
              } else {
                $status = 'Not Paid';
              }
              echo '<tr>';
              echo '<td>' . $row['order_id'] . '</td>';
              echo '<td>' . $row['product_names'] . '</td>';
              echo '<td>' . $row['product_ids'] . '</td>';
              echo '<td>' . $row['total_amount'] . '</td>';
              echo '<td>' . $row['shipping_address'] . '</td>';
              echo '<td>' . $status . '</td>';
              echo '<td>' . $row['token'] . '</td>';
              echo '<td>' .
                '<button type="button"
                 class="btn btn-primary" data-toggle="modal" data-target="#modal_' . $row['order_id'] . '">
              View </button>'
                . '</td>';
              echo '</tr>';
            }
          }
          ?>

        </tbody>
      </table>

      <!-- Modal -->
      <?php
      $resultforRecentOrder1 = $conn->query($queryforRecentOrder);

      if ($resultforRecentOrder1) {
        while ($row = $resultforRecentOrder1->fetch_assoc()) {
          echo '<div class="modal fade" id="modal_' . $row['order_id'] . '" role="dialog">';
      ?>
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Order: <?php echo $row['order_id']; ?></h4>
              </div>
              <div class="modal-body">
                <table class="table">
                  <tr>
                    <td>S.N</td>
                    <td>Product Id</td>
                    <td>Product Name</td>
                    <td>Product Size</td>
                    <td>Product Quantity</td>
                    <td>Product Price</td>
                  </tr>
                  <?php
                  $productIds = explode(',', $row['order_item_ids']);
                  $productNames = explode(',', $row['product_names']);
                  $productSizes = explode(',', $row['sizes']);
                  $productQuantities = explode(',', $row['quantities']);
                  $productPrices = explode(',', $row['prices']);
                  $numberOfProducts = count($productIds);
                  for ($i = 0; $i < $numberOfProducts; $i++) {
                    echo '<tr>';
                    echo '<td>' . $i + 1 . '</td>';
                    echo '<td>' . $productIds[$i] . '</td>';
                    echo '<td>' . $productNames[$i] . '</td>';
                    echo '<td>' . $productSizes[$i] . '</td>';
                    echo '<td>' . $productQuantities[$i] . '</td>';
                    echo '<td>' . $productPrices[$i] . '</td>';
                    echo '</tr>';
                  }
                  ?>
                  <tr>
                    <td colspan="3">Total Price</td>
                    <td colspan="3"><?php echo $row['total_amount']; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3">Payment Status</td>
                    <td colspan="3"><?php echo $row['payment_status']; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3">Payment Method</td>
                    <td colspan="3">
                      <?php
                      if ($row['payment_status'] === 'paid') {
                        echo "Paid Via Khalti";
                      } else {
                        echo "Cash On Delivery";
                      } ?></td>
                  </tr>
                  <tr>
                    <td colspan="3">Token</td>
                    <td colspan="3"><?php echo $row['token']; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3">Shipping address</td>
                    <td colspan="3"><?php echo $row['shipping_address']; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3">Order Status</td>
                    <td colspan="3"><?php echo $row['status']; ?></td>
                  </tr>
                </table>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
      <?php
          echo '</div>';
        }
      }
      ?>

    </div>

    <?php
    $paginationQuery = "SELECT COUNT(DISTINCT orders.order_id) as total FROM orders";
    $paginationResult = $conn->query($paginationQuery);
    $paginationRow = $paginationResult->fetch_assoc();
    $totalRecords = $paginationRow['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);
    for ($i = 1; $i <= $totalPages; $i++) {
      echo '<a style="padding-right: 15px;" href="?page=' . $i . '">' . $i . '</a> ';
    }
    ?>

  </div>

</body>
</html>