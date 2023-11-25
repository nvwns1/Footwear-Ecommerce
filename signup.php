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
            <br>
            <form class="login-form" action="">
                <label for="">UserName</label>
                <input type="text" name="" id="">
                <br>
                <label for="">Email</label>
                <input type="text" name="" id="">
                <br>
                <label for="">Password</label>
                <input type="text" name="" id="">
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
</body>

</html>