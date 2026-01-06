<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /tienda-zapatillas/public/login.php");
    exit();
}
?>
<?php

// Si no hay usuario logueado -> fuera
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Si NO es administrador -> fuera
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
include __DIR__ . '/../includes/header.php';

// Cargar conexión a BD
require_once __DIR__ . '/../../src/config/database.php';

// Cargar controlador de usuarios
require_once __DIR__ . '/../../src/controllers/UserController.php';

// Inicializar controlador
$userController = new UserController($pdo);

// Obtener usuarios
$users = $userController->getAllUsers();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración - Usuarios</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h1>Gestión de Usuarios</h1>

    <!-- Botón crear nuevo usuario -->
    <a href="user_new.php" class="btn btn-primary">➕ Crear nuevo usuario</a>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Rol</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= $u['name'] ?></td>
                <td><?= $u['email'] ?></td>

                <td>
                    <!-- Editar usuario -->
                    <a href="user_edit.php?id=<?= $u['id'] ?>" class="btn btn-warning">Editar</a>

                    <!-- Eliminar usuario -->
                    <a href="user_delete.php?id=<?= $u['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>
</body>
</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>