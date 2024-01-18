<?php
include "./partials/db.php";
$userId = "";
session_start();
$msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
//Fetch cart data base on user_id
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}
try {
    $sql = "SELECT cart.cart_id, cart.user_id, product.name, product.price, cart.size, cart.quantity
     FROM cart INNER JOIN products_table as product ON product.id = cart.product_id WHERE user_id = '$userId' ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $result = mysqli_query($conn, "DELETE FROM `cart`
    WHERE cart_id = '$delete_id'") or die('query failed');
    if ($result) {
        $_SESSION['msg'] = "Successfully deleted!!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>

</head>

<body>
    <?php
    include("components/navbar.php");
    include("components/headerImports.php");
    ?>

    <div class="dashboard-container">
        <div class="heading-section">
            <div class="text-side">
                <h1>My Cart</h1>
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
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>

                </thead>
                <tbody>
                    <?php
                    $grandTotal = 0;
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
                                <button class="delete-btn" onclick="confirmDelete(event, <?php echo $product['cart_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
            <br>
            <h3>Total Amount</h3>
            <p style="margin-bottom: 10px;">
                NPR <?php echo $grandTotal; ?> </p>

            <a class="checkout-button" href="checkout.php">Checkout</a>
        </div>
    </div>

</body>
<script>
    function confirmDelete(event, cartId) {
        event.preventDefault();
        const confirmResult = confirm("Are you sure you want to delete item with ID " + cartId + "?");

        if (confirmResult) {
            window.location.href = "cart.php?delete=" + cartId;
        } else {
            alert("Deletion canceled.");
        }
    }

    // JavaScript function to hide the message after 4 seconds
    setTimeout(function() {
        const msgElement = document.getElementById('msg');
        if (msgElement) {
            msgElement.style.display = 'none';
        }
    }, 4000);
</script>

</html>