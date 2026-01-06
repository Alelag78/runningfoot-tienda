<?php
// database.php: conecta con la base de datos usando PDO

$host = 'localhost';          // Servidor MySQL
$dbname = 'tienda_zapatillas'; // Nombre de la base de datos
$username = 'root';           // Usuario (XAMPP por defecto)
$password = '';               // Contraseña (vacía en XAMPP por defecto)

try {
    // Crear PDO y guardarlo en $pdo
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // Configurar PDO para que lance excepciones si hay error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
