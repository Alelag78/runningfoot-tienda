<?php
session_start();

// ---------------------------
// SEGURIDAD: SOLO ADMIN
// ---------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /tienda-zapatillas/public/login.php");
    exit();
}

require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/controllers/ItemController.php';
require_once __DIR__ . '/../../src/models/ItemSize.php';

$controller = new ItemController($pdo);
$itemSizeModel = new ItemSize($pdo);

// ---------------------------
// OBTENER ID DEL PRODUCTO
// ---------------------------
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: items.php");
    exit;
}

// Producto base
$item = $controller->getItem($id);

// Tallas existentes del producto (clave = talla)
$sizesRaw = $itemSizeModel->getSizesByItem($id);

// Convertimos el array para acceder por número de talla
$sizes = [];
foreach ($sizesRaw as $s) {
    $sizes[$s['size']] = $s;
}

// ---------------------------
// PROCESAR FORMULARIO
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];

    // Imagen actual por defecto
    $image = $item['image'];

    // ---------------------------
    // SUBIDA DE NUEVA IMAGEN
    // ---------------------------
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $image = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            __DIR__ . "/../" . $image
        );
    }

    // ---------------------------
    // ACTUALIZAR STOCK POR TALLAS
    // ---------------------------
    if (!empty($_POST['sizes'])) {
        foreach ($_POST['sizes'] as $size => $stock) {
            $stock = (int)$stock;

            if ($stock > 0) {
                // Si existe → actualizar
                if (isset($sizes[$size])) {
                    $itemSizeModel->updateStock($sizes[$size]['id'], $stock);
                } 
                // Si no existe → crear
                else {
                    $itemSizeModel->create($id, $size, $stock);
                }
            } else {
                // Si el stock es 0 → eliminar talla
                if (isset($sizes[$size])) {
                    $itemSizeModel->delete($id, $size);
                }
            }
        }
    }

    // ---------------------------
    // ACTUALIZAR PRODUCTO CON STOCK TOTAL
    // ---------------------------
    $sizesActuales = $itemSizeModel->getSizesByItem($id);
    $totalStock = array_sum(array_column($sizesActuales, 'stock'));

    $controller->updateItem(
        $id,
        $name,
        $description,
        $brand,
        $price,
        $totalStock,
        $image
    );

    header("Location: items.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<h2>Editar producto</h2>

<div class="container">
<form method="post" enctype="multipart/form-data">

    <label>Nombre</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required><br><br>

    <label>Descripción</label><br>
    <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea><br><br>

    <label>Marca</label><br>
    <input type="text" name="brand" value="<?= htmlspecialchars($item['brand']) ?>" required><br><br>

    <label>Precio (€)</label><br>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['price']) ?>" required><br><br>

    <label>Imagen</label><br>
    <input type="file" name="image"><br>
    <?php if ($item['image']): ?>
        <br>
        <img src="<?= '../' . $item['image'] ?>" width="120" alt="Imagen actual">
    <?php endif; ?>
    <br><br>

    <!-- STOCK POR TALLAS -->
    <div class="form-card">
    <h3>Stock por talla</h3>
    <div class="size-row">
        <?php foreach ([38, 39, 40, 41, 42, 43, 44] as $size): ?>
            <label>
                Talla <?= $size ?>:
                <input type="number" name="sizes[<?= $size ?>]" min="0"
                       value="<?= $sizes[$size]['stock'] ?? 0 ?>">
            </label>
        <?php endforeach; ?>
    </div>
</div>

<button type="submit">Actualizar producto</button>
</form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>