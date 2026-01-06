<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/Item.php';
require_once __DIR__ . '/../src/models/ItemSize.php';
require_once __DIR__ . '/includes/header.php';

// Comprobar que se recibe un ID válido por GET
if (!isset($_GET['id'])) {
    die("Producto no especificado.");
}

$itemId = $_GET['id'];
$itemModel = new Item($pdo);
$itemSizeModel = new ItemSize($pdo);

// Obtener información del producto
$item = $itemModel->getById($itemId);
if (!$item) {
    die("Producto no encontrado.");
}

// Obtener tallas disponibles para este producto
$sizes = $itemSizeModel->getSizesByItem($itemId);

// ---------------------------
// PROCESAR AÑADIR AL CARRITO
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $sizeId = $_POST['size_id'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 1);

    if (!$sizeId) {
        $error = "Debes seleccionar una talla.";
    } else {
        // Inicializar carrito si no existe
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Crear clave única por producto+talla
        $key = $itemId . '_' . $sizeId;

        // Añadir al carrito o incrementar cantidad
        if (!isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key] = [
                'id' => $itemId,
                'name' => $item['name'],
                'price' => $item['price'],
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Producto</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<div class="container">

    <!-- Nombre del producto -->
    <h1><?= htmlspecialchars($item['name']) ?></h1>

    <div class="product-detail">

        <!-- Imagen principal del producto -->
        <div>
            <img 
                src="<?= htmlspecialchars($item['image']) ?>" 
                alt="<?= htmlspecialchars($item['name']) ?>">
        </div>

        <!-- Información del producto -->
        <div>

            <p><strong>Marca:</strong> <?= htmlspecialchars($item['brand']) ?></p>

            <!-- Precio destacado -->
            <p class="product-price">
                <?= number_format($item['price'], 2) ?> €
            </p>

            <!-- Descripción SOLO aquí -->
            <p>
                <?= nl2br(htmlspecialchars($item['description'])) ?>
            </p>

            <!-- Formulario para añadir al carrito -->
            <form method="post" action="carrito.php">

                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']) ?>">
                <input type="hidden" name="price" value="<?= $item['price'] ?>">

                <!-- Selector de talla -->
                <label for="size_id">Talla:</label>
                <select name="size_id" required>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= $size['id'] ?>">
                            <?= $size['size'] ?> (<?= $size['stock'] ?> disponibles)
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="add">
                    Añadir al carrito
                </button>

            </form>

        </div>

    </div>

    <!-- Volver -->
    <a class="btn back" href="catalogo.php">← Volver al catálogo</a>

</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>