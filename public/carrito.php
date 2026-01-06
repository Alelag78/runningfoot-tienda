<?php
session_start();

require_once __DIR__ . '/../src/models/ItemSize.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/includes/header.php';

$itemSizeModel = new ItemSize($pdo);

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ---------------------------
// PROCESAR AÑADIR AL CARRITO
// ---------------------------
if (isset($_POST['add'])) {
    $id = $_POST['id'];
    $sizeId = $_POST['size_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $key = $id . '_' . $sizeId;

    if (!isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = [
            'id' => $id,
            'size_id' => $sizeId,
            'name' => $name,
            'price' => $price,
            'quantity' => 1
        ];
    } else {
        $_SESSION['cart'][$key]['quantity']++;
    }

    header("Location: carrito.php");
    exit;
}

// ---------------------------
// PROCESAR VACIAR CARRITO
// ---------------------------
if (isset($_GET['vaciar'])) {
    $_SESSION['cart'] = [];
    header("Location: carrito.php");
    exit;
}

// ---------------------------
// PROCESAR ELIMINAR PRODUCTO
// ---------------------------
if (isset($_GET['eliminar'])) {
    unset($_SESSION['cart'][$_GET['eliminar']]);
    header("Location: carrito.php");
    exit;
}

$cartEmpty = empty($_SESSION['cart']);
$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de compras</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<h1>Tu carrito</h1>

<a class="btn" href="catalogo.php">Seguir comprando</a>
<br><br>

<?php if ($cartEmpty): ?>
    <div class="empty-cart">
        <img src="uploads/logo.png" alt="Carrito vacío" class="empty-cart-img">
        <h2>Tu carrito está vacío</h2>
        <p>Parece que aún no has añadido productos.</p>
        <a class="btn" href="catalogo.php">Volver a la tienda</a>
    </div>
<?php else: ?>
    <?php foreach ($_SESSION['cart'] as $key => $item): 
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $realSize = $itemSizeModel->getSizeById($item['size_id']);
    ?>
        <div class="cart-item">
            <p><strong><?= htmlspecialchars($item['name']) ?></strong><br>
            Talla: <?= htmlspecialchars($realSize) ?><br>
            Precio: <?= $item['price'] ?> €</p>

            <p>
                Cantidad: 
                <a class="btn" href="update_cart.php?action=minus&id=<?= $key ?>">-</a>
                <?= $item['quantity'] ?>
                <a class="btn" href="update_cart.php?action=plus&id=<?= $key ?>">+</a>
            </p>

            <p>Subtotal: <strong><?= $subtotal ?> €</strong></p>

            <p><a class="btn" href="carrito.php?eliminar=<?= $key ?>">Eliminar</a></p>
        </div>
    <?php endforeach; ?>

    <p class="total">TOTAL: <?= $total ?> €</p>
    <p><a class="btn" href="carrito.php?vaciar=1">Vaciar carrito</a></p>
    <a class="btn" href="checkout_view.php">Proceder al pago</a>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>