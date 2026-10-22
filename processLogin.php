
<?php
// Inicia una sesión nueva o recupera la sesión existente.
// Debe ejecutarse antes de enviar contenido al navegador.
session_start();

// Comprueba que el formulario se haya enviado mediante POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera el nombre de usuario enviado desde login.php.
    // Si no existe la clave 'username', asigna una cadena vacía.
    $username = $_POST['username'] ?? '';

    // Recupera la contraseña enviada desde login.php.
    // Si no existe la clave 'password', asigna una cadena vacía.
    $password = $_POST['password'] ?? '';

} else {
    // Si la petición no utiliza POST, responde con el código HTTP 405.
    http_response_code(405);

    // Muestra el mensaje y detiene la ejecución del archivo.
    exit('Método de petición no permitido.');
}

// Comprueba que los valores recibidos sean cadenas de texto.
// Esto evita procesar valores de tipos inesperados.
if (!is_string($username) || !is_string($password)) {
    http_response_code(400);

    // Detiene el script porque los datos no son válidos.
    exit('Los datos recibidos no son válidos.');
}

// Elimina los espacios en blanco al principio y al final del usuario.
// No aplicamos trim() a la contraseña porque sus espacios podrían formar parte de ella.
$username = trim($username);

// Comprueba que el usuario y la contraseña no estén vacíos.
if ($username === '' || $password === '') {
    http_response_code(400);

    // Si falta alguno de los datos, termina el procesamiento.
    exit('Debes introducir usuario y contraseña.');
}
?>

<!-- Comprobación de las credenciales -->
<?php

// Compara el usuario y la contraseña con las credenciales de prueba.
// El operador && exige que ambas condiciones sean verdaderas.
if ($username === 'admin' && $password === '1234') {

    // Regenera el identificador de sesión después de autenticar
    // correctamente al usuario. true elimina la sesión anterior.
    session_regenerate_id(true);

    // Guarda el usuario autenticado dentro de la sesión.
    // La clave 'username' permitirá recuperarlo en otras páginas.
    $_SESSION['username'] = $username;

    // Redirige al usuario a index.php después del acceso correcto.
    header('Location: index.php');

    // Detiene el script para que no se ejecute el código posterior.
    exit;

} else {

    // Si el usuario o la contraseña no coinciden,
    // muestra un mensaje de error.
    echo 'Por favor, compruebe los datos introducidos';
}

?>

<?php
// Indica que las credenciales no han sido aceptadas.
// Esta instrucción solo se alcanza cuando el if anterior no termina
// mediante exit o cuando se ejecuta su rama else.
http_response_code(401);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de inicio de sesión | Casino Code</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="content">

        <section class="login-feedback login-feedback--error" role="alert">
            <p class="eyebrow">Acceso denegado</p>
            <h1>No se pudo iniciar sesión</h1>

            <!-- Informa de que las credenciales no coinciden. -->
            <p>El usuario o la contraseña no son correctos. Inténtalo de nuevo.</p>

            <!-- Enlace para volver al formulario de login. -->
            <a class="button" href="login.php">Volver a intentarlo</a>

        </section>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
