<?php
include "./partials/db.php";
session_start();
if ($_SESSION['privilege_level'] != 'admin') {
    header('location: index.php');
    exit();
}
$msg = "";
//Fetch cart data base on user_id

try {
    $sql = "SELECT * FROM orders WHERE status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $order_id = $_POST['order_id'];
    $newStatus = $_POST['status'];

    $sql = "UPDATE orders SET status = '$newStatus' WHERE order_id = '$order_id'";
    if ($conn->query($sql) === TRUE) {
        $msg =  "Status updated successfully";
        // header("location: adminDashboard.php");
        // exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }
}    ?>

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
                <h1>Order Pending</h1>
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
                $payment_status = $order['payment_status'];

                echo "<div class='small-container'";
                echo "Order ID: " . $order_id . "<br>";
                echo '<p> 
        Order Date: ' . $order_date .   '</br>' .
                    'Status: ' . $status . '<br>' .
                    'Shipping Address: ' . $shipping_address . '<br>' .
                    'Payment Method: ' . $payment_method . '<br>';

                if ($payment_method == 'online') {
                    echo 'Payment Status: ' . $payment_status . '<br>';
                }

                echo '</p>';

                echo '<h4>' . "Total Amount: " . $total_amount .  '</h4>'; ?>
                <div class="status-form">
                    <form action="" method="post">
                        <input type="text" name="order_id" value=<?php echo $order_id ?> hidden>
                        <div class="radio">
                            <input type="radio" name="status" value="pending" checked>Pending
                            <input type="radio" name="status" value="delivered">Delivered
                            <input type="radio" name="status" value="canceled">Canceled
                        </div>
                        <button style="width:200px; margin:10px;" class="page-button" onclick="">Edit Status</button>
                    </form>
                </div>
            <?php
                $items_query = "SELECT products.name, products.image_url,
                order_items.price,
                order_items.quantity FROM order_items
                INNER JOIN products_table as products ON order_items.product_id = products.id
                WHERE order_items.order_id = $order_id
                ";
                $items_result = mysqli_query($conn, $items_query) or die('Query failed');
                if (mysqli_num_rows($items_result) > 0) {
                    echo "<div class=artist-container>";

                    while ($item_row = mysqli_fetch_assoc($items_result)) {
                        $title = $item_row['name'];
                        $image_path = $item_row['image_url'];
                        $quantity = $item_row['quantity'];
                        $item_price = $item_row['price'];

                        echo '<h2>' . $title .  '</h2>';
                        echo '<img src="' . $image_path . '">';
                        echo '<p>' . "Price: " . $item_price .  '</p>';
                        echo '<p>' . "Quantity: " . $quantity .  '</p>';
                        echo "<br>";
                        echo "</div>";
                    }
                }


                echo "</div> <hr>";

            endforeach; ?>

            <!-- <table>
                <thead>
                    <tr>
                        <th>Order Id</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>

                </thead>
                <tbody>
                    <?php
                    /* $grandTotal = 0;
                    foreach ($products as $product) : ?>
                        <tr>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['size']; ?></td>
                            <td><?php
                                $totalProducts = $product['price'] * $product['quantity'];
                                $grandTotal += $totalProducts;
                                echo $totalProducts;
                                ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td class="action-buttons">
                                <!-- <button class="edit-btn">Edit</button> -->
                                <button class="delete-btn" onclick="confirmDelete(event, <?php echo $product['cart_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; */ ?>

                </tbody>
            </table> -->
        </div>

    </div>
</body>

</html>