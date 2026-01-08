<?php
session_start();

// Solo admin puede acceder
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    header("Location: /public/login.php");
    exit();
}

// Incluir base de datos y modelo User
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/User.php';

// Inicializar variables
$errors = [];
$success = null;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'cliente';

    // Validaciones básicas
    if ($name === '' || $email === '' || $password === '') {
        $errors[] = "Todos los campos obligatorios deben estar completos.";
    }

    if (empty($errors)) {
        $userModel = new User($pdo);
        $created = $userModel->create($role, $name, $email, $password);

        if ($created) {
            header("Location: users.php?msg=created");
            exit();
        } else {
            $errors[] = "Error al crear el usuario (quizá ya existe).";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Usuario</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1>Crear nuevo usuario</h1>
<p><a href="users.php">⬅ Volver</a></p>

<?php if ($errors): ?>
    <div class="error-box">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <label>Nombre completo:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <label>Rol:</label>
    <select name="role">
        <option value="cliente">Cliente</option>
        <option value="admin">Administrador</option>
    </select>

    <button type="submit">Crear usuario</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>