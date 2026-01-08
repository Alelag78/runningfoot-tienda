<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /public/login.php");
    exit();
}
?>
<?php
include __DIR__ . '/../../public/includes/header.php';
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/controllers/ItemController.php';


// Comprobar que el usuario es admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$controller = new ItemController($pdo);
$items = $controller->getAllItems();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de productos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<h2>Gestión de Productos</h2>

<a class="btn" href="item_new.php">Crear nuevo producto</a><br><br>

<?php if(isset($_GET['msg'])): ?>
    <p style="color:green;">
        <?php
            if($_GET['msg'] === 'created') echo "Producto creado correctamente.";
            if($_GET['msg'] === 'updated') echo "Producto actualizado correctamente.";
            if($_GET['msg'] === 'deleted') echo "Producto eliminado correctamente.";
        ?>
    </p>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === "item_used_in_orders"): ?>
    <p style="color:red; font-weight:bold;">
        ❌ No se puede eliminar este producto porque forma parte de pedidos.  
        En su lugar puedes poner stock 0 o marcarlo como no disponible.
    </p>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <p style="color:green; font-weight:bold;">✔️ Producto eliminado correctamente.</p>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Marca</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Imagen</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['id']) ?></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['description']) ?></td>
            <td><?= htmlspecialchars($item['brand']) ?></td>
            <td><?= htmlspecialchars($item['price']) ?> €</td>
            <td><?= htmlspecialchars($item['total_stock']) ?></td>
            <td>
                <?php if($item['image']): ?>
                    <img src="<?= '../' . $item['image'] ?>" alt="Imagen" width="80">
                <?php else: ?>
                    Sin imagen
                <?php endif; ?>
            </td>
            <td>
                <a href="item_edit.php?id=<?= $item['id'] ?>">Editar</a> |
                <a href="item_delete.php?id=<?= $item['id'] ?>" onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>