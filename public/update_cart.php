<?php
session_start();

// Obtener parámetros
$id = $_GET['id']; // Clave única item+talla
$action = $_GET['action'];

// Si no existe el item, redirigir
if (!isset($_SESSION['cart'][$id])) {
    header("Location: carrito.php");
    exit;
}

// Aumentar cantidad
if ($action === "plus") {
    $_SESSION['cart'][$id]['quantity']++;
}

// Disminuir cantidad
if ($action === "minus") {
    $_SESSION['cart'][$id]['quantity']--;

    // Eliminar producto si la cantidad llega a 0
    if ($_SESSION['cart'][$id]['quantity'] <= 0) {
        unset($_SESSION['cart'][$id]);
    }
}

// Redirigir de nuevo al carrito
header("Location: carrito.php");
exit;