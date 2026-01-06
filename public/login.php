<?php
// login.php - formulario y procesamiento de login

require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/includes/header.php';

if (isset($_GET['required']) && $_GET['required'] === 'checkout'): ?>
    <div class="alert alert-warning">
        Debes iniciar sesión para confirmar la compra.
    </div>
<?php endif;

$auth = new AuthController($pdo);
$message = '';

// Si ya estás logueado, redirigir a index (o admin si quieres)
if (!empty($_SESSION['user'] ?? null)) {
    header('Location: index.php');
    exit;
}

// Si venimos de registro exitoso (param registered=1), mostramos mensaje
$registered = isset($_GET['registered']) ? true : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Introduce email y contraseña.';
    } else {
        if ($auth->login($email, $password)) {
            // Login correcto: según rol puedes redirigir a admin o index
            $role = $_SESSION['user']['role'] ?? 'cliente';
            if ($role === 'admin' || $role === 'Administrador' || $role === 'administrador') {
                header('Location: admin/items.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $message = 'Credenciales incorrectas.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login - Tienda Zapatillas</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
  <h1>Iniciar sesión</h1>
  <?php if ($registered): ?>
    <p style="color:green;">Registro completado. Ahora inicia sesión.</p>
  <?php endif; ?>

  <?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post" action="login.php" novalidate>
    <label>Email<br>
      <input name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label><br><br>

    <label>Contraseña<br>
      <input name="password" type="password" required>
    </label><br><br>

    <button type="submit">Entrar</button>
  </form>

  <p style="text-align:center;">¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</body>
</html>
<?php require_once __DIR__ . '/includes/footer.php'; ?>