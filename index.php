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

<body>
  <?php
  include('components/navbar.php');
  ?>

  <div class="dashboard-container">
    <div class="heading-section">
      <div class="text-side">
        <h1>FootWear</h1>
        <p>Welcome to Footwear Collection</p>
      </div>

    </div>

    <br><br>
    <h2>Top Shoes</h2>
    <div class="product-container">
      <?php foreach ($products as $product) : ?>
        <div id="product-wrapper" class="product-wrapper">
          <a href="productdetail.php?id=<?php echo $product['id']; ?>" class="product-link">
            <img class="product-card-image" src=<?php echo $product['image_url']; ?> alt=""></a>
          <div class="card-back">
            <div class="close-icon">Close</div>
            <p>Size: <br>
              <?php
              $sizeList = explode(', ', $product['size_available']);
              foreach ($sizeList as $size) : ?>
                <input type="radio" name="selected_size" value="<?php echo htmlspecialchars($size); ?>">
                <?php echo htmlspecialchars($size); ?>
                </label>
              <?php endforeach; ?>
            </p>
            <button class="page-button">Add To Cart</button>
          </div>

          <div class="product-information">
            <a href="productdetail.php?id=<?php echo $product['id']; ?>" class="product-link">
              <p class="product-heading"><?php echo $product['name']; ?></p>
            </a>
            <p class="price-text">NPR <?php echo $product['price']; ?></p>
            <p class="short-description-text"><?php echo $product['short_description']; ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>

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


</body>

</html>