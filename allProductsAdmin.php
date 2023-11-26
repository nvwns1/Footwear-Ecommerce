<?php
include("partials/db.php");
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$productsPerPage = 10;

try {
    // Calculate the OFFSET value based on the current page and products per page
    $offset = ($page - 1) * $productsPerPage;

    // SQL query to retrieve a specific range of products
    $sql = "SELECT * FROM products_table LIMIT ?, ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $offset, $productsPerPage);
    $stmt->execute();

    $result = $stmt->get_result();

    // Fetch all products as an associative array
    $products = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Calculate total pages
$totalProducts = 0;

// Check if the connection is still open
if ($conn->ping()) {
    $totalProducts = $conn->query("SELECT COUNT(*) FROM products_table")->fetch_row()[0];
}

// Close the database connection
$conn->close();
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
<style>
    body {
        height: 90vh;
    }

    .dashboard-container {
        height: 100%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .action-buttons {
        display: flex;
        gap: 2px;
        justify-content: center;

    }

    .action-buttons button {
        padding: 8px;
        margin: 2px;
        cursor: pointer;
        border-radius: 10px;
        width: 100px;
    }

    .edit-btn {
        background-color: #4CAF50;
        color: white;
        border: none;
    }

    .delete-btn {
        background-color: #f44336;
        color: white;
        border: none;
    }
</style>

<body>
    <?php
    include('components/navbar.php');
    ?>

    <div class="dashboard-container">
        <div class="heading-section">
            <div class="text-side">
                <h1>All Products</h1>
                <p>All Products are displayed here</p>
            </div>
            <div class="button-side">
                <button>
                    <a class="page-button" href="addProduct.php">Add Product</a>
                </button>
            </div>
        </div>
        <p class="message" id="message"></p>
        <div class="allProduct-container">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Product Stocks</th>
                        <th>Action</th>
                    </tr>

                </thead>
                <tbody>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['price']; ?></td>
                            <td><?php echo $product['stocks']; ?></td>
                            <td class="action-buttons">
                                <button class="edit-btn" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="confirmDelete(<?php echo $product['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class='pagination'>
            <?php if ($page > 1) : ?>
                <a href='?page=<?php echo $page - 1; ?>'>&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= ceil($totalProducts / $productsPerPage); $i++) : ?>
                <a href='?page=<?php echo $i; ?>'><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < ceil($totalProducts / $productsPerPage)) : ?>
                <a href='?page=<?php echo $page + 1; ?>'>Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function editProduct(productId) {
            // Redirect to edit.php with product ID
            window.location.href = 'editProductById.php?id=' + productId;
        }

        function confirmDelete(productId) {
            var confirmation = confirm("Are you sure you want to delete this product?");
            if (confirmation) {
                // Assuming you have a function to handle the deletion

                deleteProduct(productId);
            }
        }



        function deleteProduct(productId) {
            // Send an AJAX request to a PHP script for deletion
            $.ajax({
                url: 'deleteProductById.php',
                type: 'GET',
                data: {
                    id: productId
                },
                success: function(response) {
                    // Remove the row from the table on successful deletion
                    console.log(response);
                    const message = document.getElementById("message")
                    location.reload()
                    message.textContent = "Successfully deleted!!" + response
                    // You may want to handle the response based on your needs
                },
                error: function(error) {
                    message.textContent = ('Error deleting product:', error);
                }
            });
        }
    </script>
</body>

</html>