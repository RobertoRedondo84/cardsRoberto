<!--Html de la página-->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="content">
        <h1>Bienvenido al Casino Code</h1>

        <p>Introduce tu usuario y contraseña</p>
    </div>

    <!--formulario para solicitar usuario y contraseña-->

<form action="processLogin.php" method="post"> <!--metodo post-->
 <label for="username">Nombre:</label>
 <input type="text" id="username" name="username" required>

 <label for="password">Contraseña</label>
 <input type="password" id="password" name="password" required>

 <button type="submit">Enviar</button>
</form>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
