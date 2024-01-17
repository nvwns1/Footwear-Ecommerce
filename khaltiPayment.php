<?php
include("partials/db.php");
session_start();
$userId = $_SESSION['user_id'];
echo $userId;
$data = json_decode(file_get_contents("php://input"));
$token = $data->token;
$orderId = isset($data->orderId) ? $data->orderId : "";
$status = "paid";
$sql = "UPDATE orders SET token=?, payment_status=? WHERE order_id=?";
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("sss", $token, $status, $orderId);

// Execute the statement
if ($stmt->execute()) {
    // $response = array("success" => true, "message" => "s Payment data updated successfully");
    // delete cart
    $cartDeletionSql = "DELETE FROM cart WHERE user_id=?";
    $cartDeletionStmt = $conn->prepare($cartDeletionSql);
    $cartDeletionStmt->bind_param("s", $userId);
    if ($cartDeletionStmt->execute()) {
        header("Location: orderHistory.php");
        exit();
    } else {
        echo "Error deleting cart: " . $conn->error;
    }
    // echo mysqli_query($conn, $deleteCartQuery);
    // header("Location:index.php");
} else {
    // $response = array("success" => false, "message" => "Error: " . $stmt->error);
    //delete order
    $deleteOrderQuery1 = "DELETE * FROM order_items WHERE order_id='$orderId'";
    mysqli_query($conn, $deleteOrderQuery1);
    $deleteOrderQuery2 = "DELETE * FROM orders WHERE order_id='$orderId'";
    mysqli_query($conn, $deleteOrderQuery2);
    header("Location :index.php");
    exit();
}
