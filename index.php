<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>  
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casino Code</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="content">
        <h1>Bienvenido al Casino Code</h1>

        <p>Explora nuestras emocionantes opciones de juego y disfruta de la experiencia del casino en línea.</p>

        <nav>
            <ul>
                <li><a href="blackjackRoberto.php">Jugar a Blackjack</a></li>
                <li><a href="higherRoberto.php">Jugar a Higher</a></li>
            </ul>
        </nav>

    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
