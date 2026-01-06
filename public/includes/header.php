<header>
    <div class="logo">
        <a href="/tienda-zapatillas/public/catalogo.php">
            <img src="/tienda-zapatillas/public/uploads/logo.png" 
             alt="Logo empresa" 
             width="120" 
             height="auto">
        </a>
    </div>   
    <div>
        <a href="<?=
            isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'
            ? '/tienda-zapatillas/public/admin/items.php'
            : '/tienda-zapatillas/public/catalogo.php'
        ?>" class="brand-name">RUNNING FOOT Sportswear</a>
    </div>
    <nav>
        <?php if (isset($_SESSION['user'])): ?>
            <span>Hola, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</span>
            <a href="/tienda-zapatillas/public/carrito.php">Carrito</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="/tienda-zapatillas/public/admin/items.php">Productos</a>
            <a href="/tienda-zapatillas/public/admin/admin_orders.php">Pedidos</a>
            <a href="/tienda-zapatillas/public/admin/users.php">Usuarios</a>
        <?php endif; ?>

            <a href="/tienda-zapatillas/public/logout.php">Salir</a>
        <?php else: ?>
            <a href="/tienda-zapatillas/public/login.php">Iniciar sesión</a>
            <a href="/tienda-zapatillas/public/register.php">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>