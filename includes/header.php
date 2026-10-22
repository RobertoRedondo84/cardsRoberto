<header class="site-header">
    <div class="site-header__brand">
        <h1>Casino Code</h1>
        <p>Práctica de Casino en PHP</p>
    </div>
    <?php if (isset($_SESSION['username'])): ?>
        <form action="logout.php" method="post" class="logout-form">
            <button type="submit">Cerrar sesión</button>
        </form>
    <?php endif; ?>
</header>