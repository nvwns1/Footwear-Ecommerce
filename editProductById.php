<?php
include("partials/db.php");

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

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
        header("Location: allProductsAdmin.php");
        exit();
    }
} else {
    // Redirect to the product list page if no ID is provided
    header("Location: allProductsAdmin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $discounted_price = $_POST["discounted_price"];
    $short_description = $_POST["short_description"];
    $long_description = $_POST["long_description"];
    $size_available = $_POST["size_available"];
    $stocks = $_POST["stocks"];
    $category = $_POST["category"];
    $sku = $_POST["sku"];


    $sql = "UPDATE products_table SET name = ?, price = ?, discounted_price = ?,short_description=?, long_description=?, size_available=?, stocks=?, category=?, sku=? WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssssssi',
        $name,
        $price,
        $discounted_price,
        $short_description,
        $long_description,
        $size_available,
        $stocks,
        $category,
        $sku,
        $productId
    );

    if ($stmt->execute()) {
        session_start();
        $_SESSION['edit_product_message'] = "Product updated successfully!";
        header("Location: editProductById.php?id={$productId}");
        exit();
    } else {
        $_SESSION['edit_product_message'] = "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include("components/headerImports.php");
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Footwear Ecommerce</title>
</head>

<body>
    <?php
    include('components/navbar.php');
    ?>

    <div class="dashboard-container">
        <div class="heading-section">
            <div class="text-side">
                <h1>Edit Product</h1>
            </div>

        </div>
        <p class="message">
            <?php
            if (isset($_SESSION['edit_product_message'])) {
                echo '' . $_SESSION['edit_product_message'] . '';
            } ?>
        </p>
        <div class="add-form">
            <!-- Use the fetched product details to pre-fill the form fields -->
            <form class="edit-product-form" method="POST" enctype="multipart/form-data">
                <!-- Add hidden input for product ID -->
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" required><br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required><br>

                <label for="discounted_price">Discounted Price:</label>
                <input type="number" id="discounted_price" name="discounted_price" value="<?php echo $product['discounted_price']; ?>" step="0.01"><br>

                <label for="short_description">Short Description:</label>
                <textarea id="short_description" name="short_description" required><?php echo $product['short_description']; ?></textarea><br>

                <label for="long_description">Long Description:</label>
                <textarea id="long_description" name="long_description" required><?php echo $product['long_description']; ?></textarea><br>

                <label for="size_available">Size Available:</label>
                <input type="text" id="size_available" name="size_available" value="<?php echo $product['size_available']; ?>" required><br>

                <label for="stocks">Stocks:</label>
                <input type="number" id="stocks" name="stocks" value="<?php echo $product['stocks']; ?>" required><br>

                <label for="category">Category:</label>
                <input type="text" id="category" name="category" value="<?php echo $product['category']; ?>" required><br>

                <label for="sku">SKU:</label>
                <input type="text" id="sku" name="sku" value="N/A" value="<?php echo $product['sku']; ?>" required><br>

                <!-- Add other form fields with their respective values -->

                <button type="submit" class="page-button">Update Product</button>
            </form>
            <!-- Display the current image of the product -->
            <div id="imagePreviewContainer">
                <img src="<?php echo $product['image_url']; ?>" alt="Current Product Image">
            </div>
        </div>
    </div>

    <script>
        // Function to preview the selected image
        function previewImage() {
            var input = document.getElementById("imageInput");
            var previewContainer = document.getElementById("imagePreviewContainer");
            var previewImage = document.getElementById("imagePreview");

            var file = input.files[0];

            if (file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };

                reader.readAsDataURL(file);

                // Display the image preview container
                previewContainer.style.display = "block";
            } else {
                // Hide the image preview container if no file is selected
                previewContainer.style.display = "none";
                previewImage.src = null;
            }
        }
    </script>

</body>

</html>