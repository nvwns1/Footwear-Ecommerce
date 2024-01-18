<?php
//database connection
include("partials/db.php");
session_start();

$productId = $_GET['id'];

//GET Method: Fetch data from the products_table using product_id
if (isset($_GET['id'])) {
    $sql = "SELECT * FROM products_table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        // Close the statement
        $stmt->close();
    } else {
        // Redirect to the product list page or show an error message
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect to the product list page if no ID is provided
    header("Location: index.php");
    exit();
}

$userId = "";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}

//function to add Cart
function addToCart($conn, $userId, $productId, $quantity, $refresh, $selected_size)
{
    //Query for checking if product already exists in cart or not.
    $sql = "SELECT * FROM `cart` WHERE user_id = '$userId' AND product_id = '$productId' ";
    $check_cart_num = mysqli_query($conn, $sql) or die('query failed!');

    if ($refresh) {
        header("Refresh:2");
    }

    if (mysqli_num_rows($check_cart_num) > 0) {
        echo "<script>alert('Item already in the cart.') </script>";
    } else {
        $sql = "INSERT INTO `cart`(user_id, product_id, quantity, size) VALUES ('$userId', '$productId' ,'$quantity', '$selected_size')";
        mysqli_query($conn, $sql);
    }
}

//POST REQUEST: Add to Cart
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];
    $stocks = $_POST['stocks'];
    $selected_size = $_POST['selected_size'];
    if (empty($userId)) {
        echo "<script>alert('please login')</script>";
    } else {
        if ($stocks >= $quantity) {
            addToCart($conn, $userId, $productId, $quantity, false, $selected_size);
            $msg = "Successfully added to cart";
        } else {
            $msg = "Out of Stock";
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootWear</title>
    <style>
        .product-detail-container {
            padding: 10px;
            display: flex;
            height: 70vh;
            width: 100%;
            justify-content: center;
            margin: 0 auto;
        }

        .detail-image-part {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        img {
            height: 500px;
        }

        .form {
            width: 50%;
            padding: 10px;
        }

        .detail-text-part>* {
            margin: 10px 0;
        }

        .description {
            padding: 20px;
        }

        ul {
            list-style-type: none;
        }

        .sum {
            width: 50px;
        }
    </style>
</head>

<body>
    <?php
    include("components/navbar.php");
    include("components/headerImports.php"); ?>
    <p class='message'>
        <?php
        if (isset($msg)) {
            echo $msg;
            echo "<script> 
            setTimeout(() => {
                " . 'document.querySelector(".message").innerHTML = "";' . "
            }, 4000);
            </script>";
        }

        ?>
    </p>
    <div class="product-detail-container">

        <div class="detail-image-part">
            <img src=<?php echo $product['image_url'] ?> alt="Product Image">
        </div>
        <form action="" method="post">
            <input type="text" name="productId" value=<?php echo $product['id']; ?>required hidden>
            <div class="detail-text-part">
                <h2><?php echo $product['name']; ?></h2>
                <p class="price-text">NPR <?php echo $product['price']; ?></p>
                <p class="short-description-text">

                <ul class="short-description-list">
                    <?php $shortDescriptionLines = explode("\n", $product['short_description']); ?>
                    <?php foreach ($shortDescriptionLines as $line) : ?>
                        <li><?php echo htmlspecialchars(trim($line)); ?></li>
                    <?php endforeach; ?>
                </ul>

                </p>
                <?php
                if ($product['stocks'] > 0) {
                ?>
                    <p>Size: <br>
                        <?php
                        $sizeList = explode(', ', $product['size_available']);
                        foreach ($sizeList as $size) : ?>
                            <input type="radio" name="selected_size" value="<?php echo htmlspecialchars($size); ?>" required>
                            <?php echo htmlspecialchars($size); ?>
                            </label>
                        <?php endforeach; ?>
                    </p>

                    <div class="quantity">
                        <button id="subQuantity" type="button" class="page-button sum">-</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max=<?php echo $product['stocks'] ?> required>
                        <button id="addQuantity" type="button" class="page-button sum">+</button>
                    </div>
                    <input type="number" id="stocks" name="stocks" value=<?php echo $product['stocks'] ?> hidden required>

                    <button type="submit" class="page-button" name="add_to_cart" id="addToCart">Add to cart</button>
                <?php
                } else {
                    echo "Out of Stock";
                }
                ?>
                <hr>
                <p><b>Sku:</b> <?php echo $product['sku']; ?></p>
                <p><b>Categories:</b><?php echo $product['category']; ?></p>

            </div>
        </form>
    </div>
    <hr>
    <div class="description">
        <h2>Description</h2>
        <p><?php echo $product['long_description']; ?></p>
    </div>
    <?php include("components/footer.php"); ?>
    <script>
        //Quatity add and subtract through button
        document.addEventListener('DOMContentLoaded', function() {

            const subQuantity = document.getElementById("subQuantity")
            const addQuantity = document.getElementById("addQuantity")
            const quantity = document.getElementById("quantity")
            addQuantity.addEventListener("click", () => {
                parseInt(quantity.value++);
            })
            subQuantity.addEventListener("click", () => {
                if (quantity.value > 1) {
                    parseInt(quantity.value--);
                }
            })
        })
    </script>
</body>

</html>