<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Invalid request.");
}

$name = trim($_POST["name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$cartData = $_POST["cart"] ?? "";

if ($name == "" || !preg_match('/^[0-9]{10}$/', $phone)) {
    die("Please enter valid customer details.");
}

$cart = json_decode($cartData, true);

if (!is_array($cart) || count($cart) == 0) {
    die("Your cart is empty or invalid.");
}

// Fixed menu prices for security
$menu = [
    "Veg Burger" => 50,
    "Pizza" => 100,
    "Sandwich" => 40,
    "French Fries" => 60,
    "Cold Coffee" => 30,
    "Tea" => 15
];

$total = 0;
$orderedItems = [];

foreach ($cart as $item) {

    $itemName = $item["name"] ?? "";
    $quantity = filter_var(
        $item["quantity"] ?? 0,
        FILTER_VALIDATE_INT
    );

    if (!isset($menu[$itemName]) ||
        $quantity === false ||
        $quantity < 1 ||
        $quantity > 50) {
        die("Invalid food item or quantity.");
    }

    $price = $menu[$itemName];
    $subtotal = $price * $quantity;
    $total += $subtotal;

    $orderedItems[] = [
        "name" => $itemName,
        "price" => $price,
        "quantity" => $quantity
    ];
}

$items = json_encode($orderedItems);

$stmt = $conn->prepare(
    "INSERT INTO orders
    (student_name, phone, items, total)
    VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("sssd", $name, $phone, $items, $total);

if ($stmt->execute()) {

    $orderId = $stmt->insert_id;

    echo "<html>
    <head>
    <title>Order Confirmation</title>
    <style>
    body {
        font-family: Arial;
        text-align: center;
        background: #f2f2f2;
        padding: 50px;
    }
    .box {
        background: white;
        padding: 30px;
        border-radius: 10px;
        display: inline-block;
    }
    </style>
    </head>
    <body>
    <div class='box'>
    <h1 style='color:green;'>Order Successful!</h1>
    <h2>Thank You, " .
    htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .
    "!</h2>
    <p>Your order has been placed successfully.</p>
    <h3>Order Number: " . $orderId . "</h3>
    <h2>Total Amount: ₹" . number_format($total, 2) . "</h2>
    <a href='index.html'>Back to Home</a>
    </div>
    </body>
    </html>";

} else {
    echo "Error: Unable to place order.";
}

$stmt->close();
$conn->close();

?>