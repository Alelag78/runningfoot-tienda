<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /tienda-zapatillas/public/login.php");
    exit();
}
?>
<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/controllers/ItemController.php';

$controller = new ItemController($pdo);

$id = $_GET['id'] ?? null;

if ($id) {

    $result = $controller->deleteItem($id);

    if ($result === "FOREIGN_KEY_BLOCK") {
        // No se puede borrar por pedidos asociados
        header("Location: items.php?error=item_used_in_orders");
        exit;
    }

    if ($result) {
        header("Location: items.php?deleted=1");
        exit;
    }
}

header("Location: items.php?error=delete_failed");
exit;
?>