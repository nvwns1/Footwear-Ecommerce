<?php
include("partials/db.php");
include("partials/getUserSession.php");

if (isset($_POST['order'])) {
    $status = "pending";
    $address = $_POST['address'];
    $payment_method = $_POST["payment"];

    $cart_total = 0;
    $cart_products = [];



    if ($payment_method == "online") {
        $query = "SELECT cart.cart_id, cart.user_id, products.id as productId, products.name, products.image_url, products.price, cart.quantity
    FROM cart INNER JOIN products_table AS products ON products.id = cart.product_id WHERE cart.user_id = '$userId'";
        $result = mysqli_query($conn, $query) or die('query failed');
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['productId'];
                $quantity = $row['quantity'];

                // Add the product details to the cart products array
                $cart_products[] = [
                    'product_id' => $product_id,
                    'title' => $row['name'],
                    'image_url' => $row['image_url'],
                    'price' => $row['price'],
                    'quantity' => $quantity
                ];
                $sub_total = ($row['price'] * $quantity);
                $cart_total += $sub_total;
            }
        }
        if (!empty($cart_products)) {
            // Insert order into the `orders` table
            mysqli_query($conn, "INSERT INTO `orders`(`user_id`, `total_amount`, `status`, `shipping_address`, `payment_method`, `payment_status`)
            VALUES ('$userId','$cart_total','$status','$address','$payment_method','not-paid')") or die('query failed');

            // Retrieve the newly inserted order_id
            $order_id = mysqli_insert_id($conn);

            // Insert order items into the `order_items` table
            foreach ($cart_products as $product) {
                $product_id = $product['product_id'];
                $quantity = $product['quantity'];
                $order_price = $product['price'];

                $result = mysqli_query($conn, "SELECT `stocks` FROM `products_table` WHERE `id` = '$product_id'");
                $row = mysqli_fetch_assoc($result);
                $current_stock = $row['stocks'];
                // Calculate new stock
                $new_stock = $current_stock - $quantity;

                if ($new_stock >= 0) {
                    mysqli_query($conn, "INSERT INTO `order_items`(`order_id`, `product_id`, `quantity`, `price`)
                    VALUES ('$order_id','$product_id','$quantity', '$order_price')") or die('query failed');
                    // Update stock in the product table
                    mysqli_query($conn, "UPDATE `products_table` SET `stocks`='$new_stock' WHERE `id`='$product_id'") or die('Query failed');
                }
            }


            header("Location: khalticheckout.php?orderId=" . $order_id);
            exit();
        }
    }
    if ($payment_method == "COD") {
        $query = "SELECT cart.cart_id, cart.user_id, products.id as productId, products.name, products.image_url, products.price, cart.quantity
    FROM cart INNER JOIN products_table AS products ON products.id = cart.product_id WHERE cart.user_id = '$userId'";

        $result = mysqli_query($conn, $query) or die('query failed');
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['productId'];
                $quantity = $row['quantity'];

                // Add the product details to the cart products array
                $cart_products[] = [
                    'product_id' => $product_id,
                    'title' => $row['name'],
                    'image_url' => $row['image_url'],
                    'price' => $row['price'],
                    'quantity' => $quantity
                ];
                $sub_total = ($row['price'] * $quantity);
                $cart_total += $sub_total;
            }
        }
        if (!empty($cart_products)) {
            // Insert order into the `orders` table
            mysqli_query($conn, "INSERT INTO `orders`(`user_id`, `total_amount`, `status`, `shipping_address`, `payment_method`, `payment_status`)
                VALUES ('$userId','$cart_total','$status','$address','$payment_method','not-paid')") or die('query failed');

            // Retrieve the newly inserted order_id
            $order_id = mysqli_insert_id($conn);

            // Insert order items into the `order_items` table
            foreach ($cart_products as $product) {
                $product_id = $product['product_id'];
                $quantity = $product['quantity'];
                $order_price = $product['price'];

                $result = mysqli_query($conn, "SELECT `stocks` FROM `products_table` WHERE `id` = '$product_id'");
                $row = mysqli_fetch_assoc($result);
                $current_stock = $row['stocks'];
                // Calculate new stock
                $new_stock = $current_stock - $quantity;

                if ($new_stock >= 0) {
                    mysqli_query($conn, "INSERT INTO `order_items`(`order_id`, `product_id`, `quantity`, `price`)
                    VALUES ('$order_id','$product_id','$quantity', '$order_price')") or die('query failed');
                    // Update stock in the product table
                    mysqli_query($conn, "UPDATE `products_table` SET `stocks`='$new_stock' WHERE `id`='$product_id'") or die('Query failed');
                }else{
                    echo "Out of Stock";
                }
            }

            // Delete cart items from the `cart` table
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$userId'") or die('query failed');
            header("location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        .checkout {
            width: 100%;
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <?php include("components/navbar.php") ?>
    <?php include("components/headerImports.php") ?>
    <section class="checkout">
        <form action="" method="post">
            <h2>Place your order</h2>
            <label for="address">Address</label>
            <input type="text" name="address" id="address" required>

            <label>Payment method:</label>
            <label for="onlinePayment">
                <input type="radio" name="payment" id="onlinePayment" value="online" required> Online
            </label>
            <label for="codPayment">
                <input type="radio" name="payment" id="codPayment" value="COD" required> COD
            </label>

            <input type="submit" name="order" class="page-button" value="Proceed to Checkout">
        </form>
    </section>
</body>

</html>