<?php
include("partials/db.php");
include("partials/getUserSession.php");

$cart_total = 0;
$cart_products = [];

$query = "SELECT cart.cart_id, cart.user_id, products.id as productId, products.name, products.price, cart.quantity
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
            'price' => $row['price'],
            'quantity' => $quantity
        ];;
        $sub_total = ($row['price'] * $quantity);
        $cart_total += $sub_total;
    }
}

// echo json_encode($cart_products);

if (empty($cart_products)) {
    header("Location: index.php");
    exit();
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
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

</head>

<body>
    <?php include("components/navbar.php") ?>
    <?php include("components/headerImports.php") ?>
    <section class="checkout">
        <p>
        <h2>Online Payment</h2>
        <br>
        <button class="btn btn-primary" id="payment-button">Pay with Khalti</button>
        </p>
    </section>

    <script>
        const totalAmount = <?php echo $cart_total; ?>;
        const cartProducts = <?php echo json_encode($cart_products); ?>;

        let productNames = "";
        let productIds = ""

        cartProducts.forEach(product => {
            productIds += product.product_id + ', '
            productNames += product.title + ', ';
        });

        productNames = productNames.slice(0, -2);
        productIds = productIds.slice(0, -2);


        const config = {
            "publicKey": "test_public_key_b597ee27dff04c8d83d4416536e12317",
            "productIdentity": productIds,
            "productName": productNames,
            "productUrl": "http://gameofthrones.wikia.com/wiki/Dragons",
            "paymentPreference": [
                "KHALTI",
                "EBANKING",
                "MOBILE_BANKING",
                "CONNECT_IPS",
                "SCT",
            ],
            "eventHandler": {
                onSuccess(payload) {
                    const token = payload.token
                    fetch('khaltiPayment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            token,
                        }),
                    })
                    if (payload) {
                        window.location.href = 'orderHistory.php';
                    } else {
                        console.log(payload, 'not - ordered')
                    }
                },
                onError(error) {
                    window.location.href = 'cart.php';
                    console.log(error);
                },
                onClose() {
                    console.log('widget is closing');
                }
            }
        };

        var checkout = new KhaltiCheckout(config);
        console.log(totalAmount)
        var btn = document.getElementById("payment-button");
        btn.onclick = function() {
            checkout.show({
                amount: totalAmount * 100
            });
        }
    </script>
</body>

</html>