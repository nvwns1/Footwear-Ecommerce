<?php
include "./partials/db.php";
session_start();
if ($_SESSION['privilege_level'] != 'admin') {
    header('location: index.php');
    exit();
}
$recordsPerPage = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;
try {
    $sql = "SELECT  
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
WHERE orders.status = 'canceled'
GROUP BY orders.order_id
LIMIT 
$offset, $recordsPerPage";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order</title>
    <style>
        .small-container {
            margin: 20px;
        }
    </style>
</head>

<body>
    <?php include("components/navbar.php") ?>
    <?php include("components/headerImports.php") ?>
    <div class="dashboard-container">
        <div class="heading-section">
            <div class="text-side">
                <h1>Order Canceled</h1>
                <p id='msg' class="message">
                    <?php
                    if (isset($msg)) {
                        echo  $msg;
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="allProduct-container">
            <?php
            if ($orders) {
            ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order Id</th>
                            <th>Date</th>
                            <th>Order Status</th>
                            <th>Shipping Address</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Total amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($orders as $order) :
                            $order_id = $order['order_id'];
                            $order_date = $order['order_date'];
                            $total_amount = $order['total_amount'];
                            $status = $order['status'];
                            $shipping_address = $order['shipping_address'];
                            $payment_method;
                            if ($order['payment_status'] === 'paid') {
                                $payment_method = 'Paid via Khalti';
                            } else {
                                $payment_method = 'Cash on Delivery';
                            }
                            $payment_status = $order['payment_status'];
                        ?>
                            <tr>
                                <td><?php echo $order_id ?></td>
                                <td><?php echo $order_date ?></td>
                                <td><?php echo $status ?></td>
                                <td><?php echo $shipping_address ?></td>
                                <td><?php echo $payment_method ?></td>
                                <td><?php echo $payment_status ?></td>
                                <td>Rs. <?php echo $total_amount ?></td>
                                <td>

                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_<?php echo $order_id; ?>">
                                        View </button>
                                </td>
                            </tr>
                        <?php
                        endforeach; ?>

                    </tbody>
                </table>

                <?php
                foreach ($orders as $row) :
                    $order_id = $row['order_id'];
                ?>
                    <div class="modal fade" id="modal_<?php echo $order_id;  ?>" role="dialog">
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
                    </div>
            <?php
                endforeach;
            } else {
                echo "No Item Here";
            }
            ?>

        </div>
        <?php
        $paginationQuery = "SELECT COUNT(DISTINCT orders.order_id) as total FROM orders WHERE status = 'canceled'";
        $paginationResult = $conn->query($paginationQuery);
        $paginationRow = $paginationResult->fetch_assoc();
        $totalRecords = $paginationRow['total'];
        $totalPages = ceil($totalRecords / $recordsPerPage);
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a style="padding-right: 15px;" href="?page=' . $i . '">' . $i . '</a> ';
        }
        ?>
    </div>

    <script>
        setTimeout(function() {
            const msgElement = document.getElementById('msg');
            if (msgElement) {
                msgElement.style.display = 'none';
            }
        }, 4000);
    </script>
</body>

</html>