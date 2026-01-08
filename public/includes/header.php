<header>
    <div class="logo">
        <a href="/public/catalogo.php">
            <img src="/public/uploads/logo.png" 
             alt="Logo empresa" 
             width="120" 
             height="auto">
        </a>
    </div>   
    <div>
        <a href="<?=
            isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'
            ? '/public/admin/items.php'
            : '/public/catalogo.php'
        ?>" class="brand-name">RUNNING FOOT Sportswear</a>
    </div>
    <nav>
        <?php if (isset($_SESSION['user'])): ?>
            <span>Hola, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</span>
            <a href="/public/carrito.php">Carrito</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="/public/admin/items.php">Productos</a>
            <a href="/public/admin/admin_orders.php">Pedidos</a>
            <a href="/public/admin/users.php">Usuarios</a>
        <?php endif; ?>

            <a href="/public/logout.php">Salir</a>
        <?php else: ?>
            <a href="/public/login.php">Iniciar sesión</a>
            <a href="/public/register.php">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>