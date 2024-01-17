<header>
    <nav class="navbar">
        <h2>
            <a class="nav-links" href="index.php">Footwear</a>
        </h2>
        <ul>
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION["username"])) {
                $isAdmin = $_SESSION["privilege_level"];
                if ($isAdmin == "admin") {
                    echo '<li><a class="nav-links" href="adminDashboard.php">Dashboard</a></li>';
                    echo '<li><a class="nav-links" href="allProductsAdmin.php">All Products</a></li>';
                    echo '<li><a class="nav-links" href="partials/logout.php">LogOut</a></li>';
                } else {
                    echo '<li><a class="nav-links" href="index.php">All Products</a></li>';
                    echo '<li><a class="nav-links" href="partials/logout.php">LogOut</a></li>';
                    echo '<li><a class="nav-links" href="cart.php">Cart</a></li>';
                    echo '<li><a class="nav-links" href="orderhistory.php">Order</a></li>';
                }
            } else {
                echo '<li><a class="nav-links" href="index.php">All Products</a></li>';
                echo '<li><a class="nav-links" href="login.php">Login/Register</a></li>';
                echo '<li><a class="nav-links" href="cart.php">Cart</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>