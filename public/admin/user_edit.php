<?php
session_start();

// Solo admin puede acceder
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    header("Location: /tienda-zapatillas/public/login.php");
    exit();
}

// Incluir base de datos y modelo User
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/User.php';

$errors = [];
$success = null;

$userModel = new User($pdo);

// Obtener ID del usuario a editar
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: users.php?error=no_id");
    exit();
}

$user = $userModel->getById($id);

if (!$user) {
    header("Location: users.php?error=not_found");
    exit();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'cliente';

    if ($name === '' || $email === '') {
        $errors[] = "Nombre y email son obligatorios.";
    }

    if (empty($errors)) {
        $updated = $userModel->update($id, $role, $name, $email, $password);

        if ($updated) {
            header("Location: users.php?msg=updated");
            exit();
        } else {
            $errors[] = "Error al actualizar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Usuario</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1>Editar usuario</h1>
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
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Contraseña (dejar en blanco para no cambiarla):</label>
    <input type="password" name="password">

    <label>Rol:</label>
    <select name="role">
        <option value="cliente" <?= $user['role'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
    </select>

    <button type="submit">Actualizar usuario</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>