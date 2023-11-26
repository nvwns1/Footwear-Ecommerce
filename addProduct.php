<?php
include("partials/db.php");

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $name = $_POST["name"];
    $price = $_POST["price"];
    $discounted_price = $_POST["discounted_price"];
    $short_description = $_POST["short_description"];
    $long_description = $_POST["long_description"];
    $size_available = $_POST["size_available"];
    $stocks = $_POST["stocks"];
    $category = $_POST["category"];
    $sku = $_POST["sku"];
    
    $image  = $_FILES["imageInput"];
    $image_name = $image['name'];
    $image_tmp = $image['tmp_name'];
    $image_path = 'photo/' . $image_name;
    move_uploaded_file($image_tmp, $image_path);
    

   // SQL query to insert data into the products_table
$sql = "INSERT INTO products_table (name, price, discounted_price, short_description, long_description, size_available, stocks, category, sku, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param('ssssssssss', $name, $price, $discounted_price, $short_description, $long_description, $size_available, $stocks, $category, $sku,$image_path);

// Execute the statement
if ($stmt->execute()) {
    echo "Product added successfully!";
} else {
    echo "Error: " . $stmt->error;
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
    <title>Footwear - Ecommerce</title>
</head>

<body>
    <?php
    include('components/navbar.php');
    ?>

    <div class="dashboard-container">
        <div class="heading-section">
            <div class="text-side">
                <h1>Add Product</h1>
            </div>
         
        </div>
        <div class="add-form">
            <form class="add-product-form" method="POST" enctype="multipart/form-data">

                <!-- Image input and preview container -->
                <label for="imageInput">Upload Image:</label>
                <input type="file" name="imageInput" id="imageInput" accept="image/*" onchange="previewImage()">

                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required><br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required><br>

                <label for="discounted_price">Discounted Price:</label>
                <input type="number" id="discounted_price" name="discounted_price" step="0.01"><br>

                <label for="short_description">Short Description:</label>
                <textarea id="short_description" name="short_description" required></textarea><br>

                <label for="long_description">Long Description:</label>
                <textarea id="long_description" name="long_description" required></textarea><br>

                <label for="size_available">Size Available:</label>
                <input type="text" id="size_available" name="size_available" required><br>

                <label for="stocks">Stocks:</label>
                <input type="number" id="stocks" name="stocks" required><br>

                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required><br>

                <label for="sku">SKU:</label>
                <input type="text" id="sku" name="sku" value="N/A" required><br>


                <button type="submit" class="page-button">Add Product</button>
            </form>
            <div id="imagePreviewContainer">
                <img id="imagePreview" alt="Image Preview">
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