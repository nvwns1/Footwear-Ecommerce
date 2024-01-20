<?php
$didMatch = false;
$empty = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("partials/db.php");
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    $usernameCheck = "SELECT * FROM `users`   WHERE  `username`='$username'";
    $resOfCheck = mysqli_query($conn, $usernameCheck);
    $numOfUserName = mysqli_num_rows($resOfCheck);

    if ($numOfUserName > 0) {
        $didMatch = true;
    } else {
        if ($username == "" || $password == "" || $email == "") {
            $empty = true;
        } else if (($password != "") && ($exist == false)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $q = "INSERT INTO `users`(`username`, `password`, `email`) VALUES ('$username','$passwordHash','$email')";
            $result1 = mysqli_query($conn, $q);
            if ($result1) {
                session_start();
                $_SESSION["username"] = $username;
                $_SESSION["privilege_level"] = $privilege_level;
                header("location: index.php");
            }
        } else {
            $error = "An error occured. Please try again later.";
        }
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
    include('components/navbar.php');
    ?>
    <div class="login-page-container">
        <div class="login-container">
            <h2>Signup Form</h2>
            <p class="login-error">
                <?php
                if (isset($didMatch) && $didMatch) {
                    echo "Username already exists.";
                } else if (isset($empty) && $empty) {
                    echo "Please fill all fields";
                } else {
                    echo "";
                }
                ?>
            </p>
            <form class="login-form" method='post'>
                <label for="">UserName</label>
                <input type="text" name="username" id="username" required>
                <br>
                <label for="">Email</label>
                <input type="email" name="email" id="email" required>
                <br>
                <label for="">Password</label>
                <div style="display: flex;">
                    <input style="width: 100%;" type="password" name="password" id="passwordInput" required>
                    <button style="width: 50px;" id="passwordVisibility" type="button">Show</button>
                </div>
                <br>
                <button class="page-button" type="submit">Signup</button>
            </form>
        </div>
        <div class="small-container">
            Already have an account?
            <span><a href="login.php">Login in</a></span>
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