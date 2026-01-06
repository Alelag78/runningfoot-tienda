<?php
// register.php - formulario y procesamiento de registro

// cargamos la conexión y el controlador (asegúrate que existan en src/)
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/includes/header.php';

// Creamos el controller (arranca sesión en su constructor)
$auth = new AuthController($pdo);

$message = '';

// Si se envía el formulario (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos y saneamos entrada básica
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validaciones mínimas
    if ($name === '' || $email === '' || $password === '') {
        $message = 'Rellena los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email no válido.';
    } elseif ($password !== $password2) {
        $message = 'Las contraseñas no coinciden.';
    } else {
        // Intentamos registrar (crear es método de User dentro del controlador)
        try {
            $auth->register($name, $email, $password);
            // Redirigir al login tras registro exitoso
            header('Location: login.php?registered=1');
            exit;
        } catch (Exception $e) {
            $message = 'Error al registrar: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro - Tienda Zapatillas</title>
  <link rel="stylesheet" href="css/styles.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
  <h1>Registro de usuario</h1>

  <?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post" action="register.php" novalidate>
    <label>Nombre (obligatorio)<br>
      <input name="name" type="text" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </label><br><br>

    <label>Email (obligatorio)<br>
      <input name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label><br><br>

    <label>Contraseña (obligatorio)<br>
      <input name="password" type="password" required>
    </label><br><br>

    <label>Repite la contraseña<br>
      <input name="password2" type="password" required>
    </label><br><br>

    <label>Teléfono (opcional)<br>
      <input name="phone" type="text" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </label><br><br>

    <label>Dirección (opcional)<br>
      <textarea name="address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
    </label><br><br>

    <button type="submit">Registrarme</button>
  </form>

  <p style="text-align:center;">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>
<?php require_once __DIR__ . '/includes/footer.php'; ?>