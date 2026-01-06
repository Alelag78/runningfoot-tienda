<?php
session_start();

require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/Order.php';
require_once __DIR__ . '/includes/header.php';

// Comprobar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
    header("Location: login.php?required=checkout");
    exit;
}

// Comprobar que el carrito no esté vacío
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("<p>El carrito está vacío.</p><a class='btn' href='catalogo.php'>Volver al catálogo</a>");
}

// Crear instancia del modelo Order
$orderModel = new Order($pdo);
$userId = $_SESSION['user']['id'];

// Crear el pedido en la base de datos
$orderId = $orderModel->createOrder($userId, $_SESSION['cart']);

// Limpiar el carrito
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra confirmada</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .confirmation {
            background: #e6ffe6;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #00cc00;
            margin-top: 20px;
        }
        .confirmation h1 {
            color: #008000;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: bold;
        }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="confirmation">
    <h1>¡Compra realizada con éxito!</h1>
    <p>Tu pedido <strong>#<?= $orderId ?></strong> ha sido registrado correctamente.</p>
    <p>Gracias por comprar en nuestra tienda de zapatillas.</p>
    <a class="btn" href="catalogo.php">Volver al catálogo</a>
</div>

</body>
</html>

<?php require_once __DIR__ . '/includes/footer.php'; ?>