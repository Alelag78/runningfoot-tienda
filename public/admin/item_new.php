<?php
session_start();

// ---------------------------
// SEGURIDAD: SOLO ADMIN
// ---------------------------
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /tienda-zapatillas/public/login.php");
    exit();
}

include __DIR__ . '/../../public/includes/header.php';
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/controllers/ItemController.php';
require_once __DIR__ . '/../../src/models/ItemSize.php';

$controller = new ItemController($pdo);
$itemSizeModel = new ItemSize($pdo);

// ---------------------------
// PROCESAR FORMULARIO
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Datos básicos del producto
    $name = $_POST['name'];
    $description = $_POST['description'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $image = null;

    // ---------------------------
    // SUBIDA DE IMAGEN
    // ---------------------------
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $image = $targetDir . basename($_FILES['image']['name']);

        // Se guarda dentro de /public/uploads
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            __DIR__ . "/../" . $image
        );
    }

    // ---------------------------
    // CREAR PRODUCTO
    // ---------------------------
    // IMPORTANTE: devolver ID del item recién creado
    $itemId = $controller->createItem(
        $name,
        $description,
        $brand,
        $price,
        0,       // stock general a 0 (se usa item_sizes)
        $image
    );

    // ---------------------------
    // GUARDAR STOCK POR TALLAS
    // ---------------------------
    if (!empty($_POST['sizes'])) {
        foreach ($_POST['sizes'] as $size => $stock) {
            // Solo guardamos tallas con stock > 0
            if ($stock > 0) {
                $itemSizeModel->create($itemId, $size, $stock);
            }
        }
    }

    header("Location: items.php?msg=created");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de productos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<h2>Crear nuevo producto</h2>

<form method="post" enctype="multipart/form-data">

    <label>Nombre</label><br>
    <input type="text" name="name" required><br><br>

    <label>Descripción</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Marca</label><br>
    <input type="text" name="brand" required><br><br>

    <label>Precio (€)</label><br>
    <input type="number" step="0.01" name="price" required><br><br>

    <label>Imagen</label><br>
    <input type="file" name="image"><br><br>

    <!-- ---------------------------
         STOCK POR TALLAS
    ---------------------------- -->
    <div class="form-card">
    <h3>Stock por talla</h3>
    <div class="size-row">
    <?php foreach ([38, 39, 40, 41, 42, 43, 44] as $size): ?>
        <label class="fornm-row">
            Talla <?= $size ?>:
            <input
                type="number"
                name="sizes[<?= $size ?>]"
                value="0"
                min="0"
            >
        </label>
        <br>
    <?php endforeach; ?>
    </div>
    <br>
    <button type="submit">Crear producto</button>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>