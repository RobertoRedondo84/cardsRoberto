<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?> 
<?php

require_once __DIR__ . '/getdeck.php';

// Obtener el mazo de cartas y barajarlo
$deck = getDeck();

// Quitar los comodines del mazo (se quitan las dos últimas cartas que son los comodines)
array_pop($deck);
array_pop($deck);

// Barajar el mazo
shuffle($deck);

// Crear un array para almacenar las cartas de cada participante
$participants = [
    'Banca' => [],
    'Jugador 1' => [],
    'Jugador 2' => [],
    'Jugador 3' => [],
    'Jugador 4' => [],
    'Jugador 5' => [],
];

/**
 * Repartir dos cartas a cada participante
 * Se reparten dos rondas de cartas, una carta por participante en cada ronda
 */

for ($round = 0; $round < 2; $round++) {
    foreach ($participants as $name => $cards) { // Repartir una carta a cada participante (se hace 2 veces)
        $participants[$name][] = array_pop($deck); // Tomar la última carta del mazo y asignarla al participante (2 veces por cada participante)
    }
}

/**
 * Reparte cartas automáticamente hasta que cada participante
 * alcanza una puntuación igual o superior a 14.
 */
foreach ($participants as $name => $cards) {
    while (calculateScore($participants[$name]) < 14) { // Mientras la puntuación del participante sea menor a 14, se le reparte otra carta
        $participants[$name][] = array_pop($deck); // Tomar la última carta del mazo y asignarla al participante
    }
}

/**
 * Compara la puntuación de cada jugador con la de la banca.
 */
$bankScore = calculateScore($participants['Banca']);
$results = [];

foreach ($participants as $name => $cards) {
    // La banca no se compara consigo misma.
    if ($name === 'Banca') {
        continue;
    }

    $playerScore = calculateScore($cards);

    // Determina el resultado aplicando las reglas del enunciado.
    if ($playerScore > 21) {
        $result = 'PIERDE';
    } elseif ($bankScore > 21) {
        $result = 'GANA';
    } elseif ($playerScore > $bankScore) {
        $result = 'GANA';
    } elseif ($playerScore < $bankScore) {
        $result = 'PIERDE';
    } else {
        $result = 'EMPATA';
    }

    // Guarda la puntuación y el resultado de cada jugador.
    $results[$name] = [
        'score' => $playerScore,
        'result' => $result,
    ];
}


/**
 * Calcula la puntuación de una mano de Blackjack.
 *
 * @param array $cards Cartas que tiene el participante.
 * @return int Puntuación total de la mano.
 */ 

function calculateScore(array $cards): int
{
    $score = 0;
    $aces = 0;

    foreach ($cards as $card) {
        $value = $card['value'];

        if ($value === 1) {
            $score += 11;
            $aces++;
        } elseif ($value >= 11 && $value <= 13) {
            $score += 10;
        } else {
            $score += $value;
        }
    }

    while ($score > 21 && $aces > 0) {
        $score -= 10;
        $aces--;
    }

    return $score;
}

?>
<!--html del juego Blackjack-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blackjack | Casino Code</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    
<main class="blackjack-game">
    <header class="blackjack-intro">
        <p class="eyebrow">Casino Code presenta</p>
        <h1>Blackjack</h1>
        <p>Seis participantes, una banca y una partida automática.</p>
    </header>

    <section class="blackjack-bank">
        <h2>Banca</h2>

        <p class="blackjack-score">
            Puntuación: <strong><?= $bankScore ?></strong>
        </p>

        <ul class="card-list">
            <?php foreach ($participants['Banca'] as $card): ?>
                <li>
                    <img
                        src="images/<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($card['suit'] . ' ' . $card['value'], ENT_QUOTES, 'UTF-8') ?>"
                        loading="lazy"
                    >
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="blackjack-players">
        <h2>Resultados de la partida</h2>

        <?php foreach ($results as $name => $result): ?>
            <article class="blackjack-player">
                <h3><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h3>

                <p class="blackjack-score">
                    Puntuación: <strong><?= $result['score'] ?></strong>
                </p>

                <p class="blackjack-result">
                    Resultado:
                    <strong><?= htmlspecialchars($result['result'], ENT_QUOTES, 'UTF-8') ?></strong>
                </p>

                <ul class="card-list">
                    <?php foreach ($participants[$name] as $card): ?>
                        <li>
                            <img
                                src="images/<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($card['suit'] . ' ' . $card['value'], ENT_QUOTES, 'UTF-8') ?>"
                                loading="lazy"
                            >
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </section>

    <nav class="blackjack-actions" aria-label="Acciones de la partida">
        <a class="back-link" href="blackjackRoberto.php">Nueva partida</a>
        <a class="back-link" href="index.php">Volver al inicio</a>
    </nav>
</main>


    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>