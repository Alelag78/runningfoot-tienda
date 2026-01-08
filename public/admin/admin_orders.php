<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /public/login.php");
    exit();
}

require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/controllers/OrdersController.php';
require_once __DIR__ . '/../includes/header.php';

$controller = new OrdersController($pdo);
$orders = $controller->getAllOrders();

// ---------------------------
// BORRAR PEDIDO
// ---------------------------
if (isset($_GET['delete_order'])) {
    $orderId = $_GET['delete_order'];
    $controller->deleteOrder($orderId);
    header("Location: admin_orders.php?deleted=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de pedidos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<h1>Pedidos de la tienda</h1>
<a class="btn" href="items.php">Volver a administración</a>
<br><br>

<?php if (isset($_GET['deleted'])): ?>
    <p style="color:green; font-weight:bold;">✔️ Pedido eliminado correctamente.</p>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <p>No hay pedidos aún.</p>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <h3>
                Pedido #<?= $order['order_id'] ?> - <?= $order['created_at'] ?>
                <a href="admin_orders.php?delete_order=<?= $order['order_id'] ?>" 
                   onclick="return confirm('¿Eliminar este pedido? Esta acción no se puede deshacer');" 
                   style="color:red; font-size:14px; margin-left:10px;">Eliminar</a>
            </h3>
            <p>Cliente: <?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['user_email']) ?>)</p>
            <p>Total: <?= $order['total'] ?> €</p>

            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['size']) ?></td>
                        <td><?= $item['unit_price'] ?> €</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['unit_price'] * $item['quantity'] ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <br>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>