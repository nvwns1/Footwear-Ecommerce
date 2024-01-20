<?php
$error = false;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("./partials/db.php");

    $username = $_POST["username"];
    $password = $_POST["password"];
    $userCheck = "SELECT * from users WHERE `username` = '$username'";
    $resultOfUserCheck = mysqli_query($conn, $userCheck);
    $numOfUser = mysqli_num_rows($resultOfUserCheck);

    if ($numOfUser == 1) {
        while ($row = mysqli_fetch_array($resultOfUserCheck)) {
            if (password_verify($password, $row["password"])) {
                session_start();
                $_SESSION['loggedIn'] = true;
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['privilege_level'] = $row['privilege_level'];
                if ($_SESSION['privilege_level'] == 'admin') {
                    header('location: adminDashboard.php');
                    exit();
                } else {
                    header('location: index.php');
                    exit();
                }
            }
        }
    } else {
        $error = true;
    }
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
    if (isset($_SESSION["username"])) {
        echo $_SESSION['username'];
    } ?>
    <?php
    include('components/navbar.php');
    ?>
    <div class="login-page-container">
        <div class="login-container">
            <h2>Login Form</h2>
            <p class="login-error">
                <?php
                if (isset($error) && $error) {
                    echo "Invalid username and password";
                }
                ?>
            </p>
            <form class="login-form" method="post">
                <label for="">UserName</label>
                <input type="text" name="username" id="" required>
                <br>
                <label for="">Password</label>
                <div style="display: flex;">
                    <input style="width: 100%;" type="password" name="password" id="passwordInput" required>
                    <button style="width: 50px;" id="passwordVisibility" type="button">Show</button>
                </div>
                <br>
                <button class="page-button" type="submit">Login</button>
            </form>
        </div>
        <div class="small-container">
            Don't have an account?
            <span><a href="Signup.php">Sign up</a></span>
        </div>
    </div>

    <?php
    include('components/footer.php');
    ?>
    <script>
        const button = document.getElementById("passwordVisibility");
        const passwordInput = document.getElementById("passwordInput")
        button.addEventListener("click", () => {
            const btnValue = button.innerText
            if (btnValue === 'Show') {
                passwordInput.type = 'text'
                button.innerText = "Hide"
            } else {
                passwordInput.type = 'password'
                button.innerText = "Show"
            }
        })
    </script>
</body>

</html>