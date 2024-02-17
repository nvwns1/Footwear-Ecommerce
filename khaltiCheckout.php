<?php
include("partials/db.php");
include("partials/getUserSession.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : "";

    $query = "SELECT * from orders WHERE order_id = '$orderId'";
    $result = mysqli_query($conn, $query) or die('query failed');
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $order_id = $row['order_id'];
            $user_id = $row['user_id'];
            $total_amount = $row['total_amount'];
        }

        $query2 = "SELECT name FROM order_items JOIN products_table ON products_table.id = order_items.product_id WHERE order_id = '$orderId'";
        $result2 = mysqli_query($conn, $query2);

        if ($result2) {
            while ($row = mysqli_fetch_assoc($result2)) {
                $productName = $row['name'];
            }
        } else {
            // Handle the query error, e.g., display an error message
            echo "Error in query: " . mysqli_error($conn);
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
        function getOrderIdFromQueryString() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('orderId') || '';
        }
        const totalAmount = <?php echo json_encode($total_amount); ?>;
        const productName = <?php echo json_encode($productName); ?>;
        const config = {
            // replace the publicKey with yours
            "publicKey": "test_public_key_b597ee27dff04c8d83d4416536e12317",
            "productIdentity": "1234567890",
            "productName": productName,
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
                    const orderId = getOrderIdFromQueryString();
                    fetch('khaltiPayment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            token,
                            orderId
                        }),
                    })
                    if (payload) {
                        window.location.href = 'orderHistory.php';
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
            // minimum transaction amount must be 10, i.e 1000 in paisa.
            checkout.show({
                amount: totalAmount * 100
                // amount: 1000
            });
        }
    </script>
</body>

</html>