<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/Item.php';
require_once __DIR__ . '/../src/models/ItemSize.php';
require_once __DIR__ . '/includes/header.php';

// Instanciar modelos
$itemModel = new Item($pdo);
$itemSizeModel = new ItemSize($pdo);

// Obtener todos los productos
$items = $itemModel->getAll();

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ---------------------------
// PROCESAR AÑADIR AL CARRITO
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $itemId = $_POST['item_id'];
    $sizeId = $_POST['size_id'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 1);

    if (!$sizeId) {
        $error = "Debes seleccionar una talla.";
    } else {
        $itemData = $itemModel->getById($itemId);
        if (!$itemData) {
            $error = "Producto no encontrado.";
        } else {
            $key = $itemId . '_' . $sizeId;

            if (!isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key] = [
                    'id' => $itemId,
                    'name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'size_id' => $sizeId,
                    'quantity' => $quantity
                ];
            } else {
                $_SESSION['cart'][$key]['quantity'] += $quantity;
            }

            header("Location: carrito.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de productos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<div class="container">
    <h1>Catálogo de productos</h1>

    <?php if (!empty($error)) : ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- GRID DEL CATÁLOGO -->
    <div class="catalogo">
        <?php foreach ($items as $item): 
            $sizes = $itemSizeModel->getSizesByItem($item['id']);
        ?>
            <div class="product-card">

                <!-- Imagen -->
                <img src="<?= htmlspecialchars($item['image']) ?>"
                     alt="<?= htmlspecialchars($item['name']) ?>">

                <!-- Contenido -->
                <div class="product-body">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="price"><?= number_format($item['price'], 2) ?> €</p>

                    <!-- Formulario compacto -->
                    <form method="post" class="product-form">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">

                        <select name="size_id" required>
                            <option value="">Talla</option>
                            <?php foreach ($sizes as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['size']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="number"
                               name="quantity"
                               value="1"
                               min="1"
                               class="qty-input">

                        <button type="submit" name="add" class="btn-add">
                            Añadir
                        </button>
                    </form>

                    <a class="product-link" href="producto.php?id=<?= $item['id'] ?>">
                        Ver detalles →
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>