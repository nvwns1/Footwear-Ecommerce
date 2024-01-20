<?php
session_start();
$user_id;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: index.php");
    exit();
}
