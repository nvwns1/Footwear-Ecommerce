<?php
include "./partials/db.php";
$userId = "";
$msg = "";
include("partials/getUserSession.php");

try {
    $recordsPerPage = 5;
    $current_page = isset($_Get['page']) ? $_GET['page'] : 1;
    $offset = ($current_page - 1) * $recordsPerPage;
    $sql = "SELECT * FROM orders WHERE user_id = '$userId' LIMIT $offset, $recordsPerPage";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

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
                <h1>Order History</h1>
            </div>
        </div>
        <div class="allProduct-container">
            <?php
            foreach ($orders as $order) :
                $order_id = $order['order_id'];
                $order_date = $order['order_date'];
                $total_amount = $order['total_amount'];
                $status = $order['status'];
                $shipping_address = $order['shipping_address'];
                $payment_method = $order['payment_method'];

                $items_query = "SELECT products.name, products.image_url,
                order_items.price,
                order_items.quantity FROM order_items
                INNER JOIN products_table as products ON order_items.product_id = products.id
                WHERE order_items.order_id = $order_id
                ";
                $items_result = mysqli_query($conn, $items_query) or die('Query failed');

            ?>
                <div class="small-container">
                    <table class="table">
                        <?php
                        if (mysqli_num_rows($items_result) > 0) {
                        }
                        ?>
                        <tr>
                            <td>Order Id</td>
                            <td colspan="3"><?php echo $order_id ?></td>
                        </tr>
                        <tr>
                            <td>Product</td>
                            <td>Quantity</td>
                            <td>Price</td>
                            <td>Image</td>
                        </tr>
                        <?php

                        while ($item_row = mysqli_fetch_assoc($items_result)) {
                            $title = $item_row['name'];
                            $image_path = $item_row['image_url'];
                            $quantity = $item_row['quantity'];
                            $item_price = $item_row['price'];
                        ?>
                            <tr>
                                <td><?php echo $title ?></td>
                                <td><?php echo $quantity ?></td>
                                <td><?php echo $item_price ?></td>
                                <td><img style="height: 50px; width=50px;" src=<?php echo $image_path ?>></td>
                            </tr>
                        <?php
                        }

                        ?>
                        <tr>
                            <td>Order Date</td>
                            <td colspan="3"><?php echo $order_date ?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td colspan="3"><?php echo $status ?></td>
                        </tr>
                        <tr>
                            <td>Shipping Address</td>
                            <td colspan="3"><?php echo $shipping_address ?></td>
                        </tr>
                        <tr>
                            <td>Payment Method</td>
                            <td colspan="3"><?php
                                            ($payment_method === 'online') ?
                                                $p =  "Paid via khalti"
                                                : "Cash On Delivery";
                                            echo $p;
                                            ?></td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <th colspan="3"><?php echo $total_amount ?></th>
                        </tr>
                    </table>
                </div>

                <hr>
            <?php

            endforeach; ?>
        </div>
        <?php
        $paginationQuery = "SELECT COUNT(DISTINCT  orders.order_id) as total FROM orders WHERE orders.user_id = $userId";
        $paginationResult = $conn->query($paginationQuery);
        $paginationRow = $paginationResult->fetch_assoc();
        $totalRecords = $paginationRow['total'];
        $totalPages = ceil($totalRecords / $recordsPerPage);
        for ($i = 1; $i < $totalPages; $i++) {
            echo '<a style="padding-right: 15px;" href="?page=' . $i . '">' . $i . '</a> ';
        }
        ?>
    </div>

</body>

</html>