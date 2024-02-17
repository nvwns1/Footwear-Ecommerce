<?php
include("partials/db.php");
include("partials/getUserSession.php");

$data = json_decode(file_get_contents("php://input"));
$token = $data->token;
$shippingAddress = isset($_SESSION['shipping_address']) ? $_SESSION['shipping_address'] : '';

$query = "SELECT cart.cart_id, cart.user_id, products.id as productId, products.name, products.image_url, products.price, cart.quantity
    FROM cart INNER JOIN products_table AS products ON products.id = cart.product_id WHERE cart.user_id = '$userId'";
$result = mysqli_query($conn, $query) or die('query failed');
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_id = $row['cart_id'];

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


// Insert order into the `orders` table
mysqli_query($conn, "INSERT INTO `orders`(`user_id`, `total_amount`, `status`, `shipping_address`, `payment_method`, `payment_status`, `token`)
            VALUES ('$userId','$cart_total','pending','$shippingAddress','online','paid', '$token')") or die('query failed');

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

if (isset($_SESSION['shipping_address'])) {
    unset($_SESSION['shipping_address']);
}

// delete cart
$cartDeletionSql = "DELETE FROM cart WHERE user_id=?";
$cartDeletionStmt = $conn->prepare($cartDeletionSql);
$cartDeletionStmt->bind_param("s", $userId);
if ($cartDeletionStmt->execute()) {
    header("Location: orderHistory.php");
    exit();
} else {
    echo "Error deleting cart: " . $conn->error;
}
