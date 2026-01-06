<?php
session_start();

require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/Item.php';
require_once __DIR__ . '/../src/models/ItemSize.php';
require_once __DIR__ . '/includes/header.php';

// Si carrito vacío → volver a catálogo
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: catalogo.php");
    exit;
}

$itemModel = new Item($pdo);
$itemSizeModel = new ItemSize($pdo);

// Preparar lista de productos con tallas y subtotal
$items = [];
$total = 0;

foreach ($_SESSION['cart'] as $key => $cartItem) {
    $producto = $itemModel->getById($cartItem['id']);
    if ($producto) {
        $producto['qty'] = $cartItem['quantity'];
        $producto['size_id'] = $cartItem['size_id']; // Guardamos talla
        $producto['size'] = $itemSizeModel->getSizeById($cartItem['size_id']);
        $producto['subtotal'] = $producto['price'] * $cartItem['quantity'];
        $total += $producto['subtotal'];
        $items[] = $producto;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar pedido</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<h1>Revisión del pedido</h1>

<table border="1">
    <tr>
        <th>Producto</th>
        <th>Talla</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Subtotal</th>
    </tr>

    <?php foreach ($items as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['size']) ?></td>
            <td><?= $p['qty'] ?></td>
            <td><?= $p['price'] ?> €</td>
            <td><?= $p['subtotal'] ?> €</td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Total: <?= $total ?> €</h2>

<!-- Botón para confirmar compra -->
<form action="checkout.php" method="post">
    <button type="submit">Confirmar compra</button>
</form>

<a href="carrito.php">Volver al carrito</a>

</body>
</html>
<?php require_once __DIR__ . '/includes/footer.php'; ?>