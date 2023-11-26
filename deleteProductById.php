<?php

// Function to establish a PDO connection to the database
function connectToDatabase()
{
    $host = "localhost"; // Replace with your database host
    $dbname = "footwear"; // Replace with your database name
    $username = "root"; // Replace with your database username
    $password = ""; // Replace with your database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error connecting to the database: " . $e->getMessage());
    }
}


// Function to delete a product from the database
function deleteProduct($pdo, $productId)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM products_table WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return true; // Product deleted successfully
        } else {
            return false; // Product with given ID not found
        }
    } catch (PDOException $e) {
        die("Error deleting product: " . $e->getMessage());
    }
}



// Check if the request contains the product ID
if (isset($_GET['id'])) {
    // Get the product ID from the request
    $productId = $_GET['id'];

    // Connect to the database
    $pdo = connectToDatabase();

    // Attempt to delete the product
    $success = deleteProduct($pdo, $productId);

    // Send a response to the client
    if ($success) {
        echo "Product with ID {$productId} deleted successfully!";
    } else {
        echo "Failed to delete product with ID {$productId}. Product not found.";
    }
} else {
    echo "Invalid request. Product ID not provided.";
}
