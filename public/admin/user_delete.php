<?php
session_start();

// Solo admin puede acceder
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    header("Location: /public/login.php");
    exit();
}

// Incluir base de datos y controlador
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/controllers/UserController.php';

$controller = new UserController($pdo);

$id = $_GET['id'] ?? null;

if ($id) {
    $deleted = $controller->deleteUser($id);

    if ($deleted === "FOREIGN_KEY_BLOCK") {
        header("Location: users.php?error=user_has_orders");
        exit();
    }

    if ($deleted) {
        header("Location: users.php?msg=deleted");
        exit();
    }
}

// Si falla
header("Location: users.php?error=delete_failed");
exit();
?>