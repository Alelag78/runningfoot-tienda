<?php
// logout.php - cerrar sesión
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';

$auth = new AuthController($pdo);
$auth->logout();

// Redirigimos al login
header('Location: login.php');
exit;
?>