<?php
require_once __DIR__ . '/getdeck.php';
// Obtener el mazo de cartas y barajarlo
$deck = getDeck();
shuffle($deck);

$player1 = [];
$player2 = [];
// Repartir 10 cartas a cada jugador
for ($i = 0; $i < 10; $i++) {
    $player1[] = array_pop($deck);
    $player2[] = array_pop($deck);
}

$score1 = 0;
$score2 = 0;
// Comparar las cartas de cada jugador y contar las rondas ganadas
for ($i = 0; $i < 10; $i++) {
    if ($player1[$i]['value'] > $player2[$i]['value']) {
        $score1++;
    } elseif ($player1[$i]['value'] < $player2[$i]['value']) {
        $score2++;
    }
}
// Determinar el resultado final del juego
if ($score1 > $score2) {
    $result = '¡Gana el jugador 1!';
    $resultClass = 'player-one';
} elseif ($score1 < $score2) {
    $result = '¡Gana el jugador 2!';
    $resultClass = 'player-two';
} else {
    $result = '¡Empate!';
    $resultClass = 'draw';
}
?>
//html del juego Higher
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Higher | Casino Code</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="higher-game">
        <div class="higher-intro">
            <p class="eyebrow">Casino Code presenta</p>
            <h1>Higher</h1>
            <p>Diez rondas: gana quien consiga más cartas de valor superior.</p>
        </div>
//html del marcador de puntuación
        <section class="scoreboard" aria-label="Resultado de la partida">
            <div class="score">
                <span>Jugador 1</span>
                <strong><?= $score1 ?></strong>
                <span class="score-caption">rondas ganadas</span>
            </div>
            <div class="score-divider" aria-hidden="true">VS</div>
            <div class="score">
                <span>Jugador 2</span>
                <strong><?= $score2 ?></strong>
                <span class="score-caption">rondas ganadas</span>
            </div>
            <p class="game-result <?= htmlspecialchars($resultClass, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </section>
//html de las cartas de los jugadores
        <div class="players">
            <?php foreach ([
                ['name' => 'Jugador 1', 'cards' => $player1],
                ['name' => 'Jugador 2', 'cards' => $player2],
            ] as $player): ?>
                <section class="player-panel">
                    <h2><?= htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <ul class="card-list">
                        <?php foreach ($player['cards'] as $card): ?>
                            <?php
                            $suit = (string) $card['suit'];
                            $value = (string) $card['value'];
                            $alt = $suit . ' ' . $value;
                            ?>
                            <li>
                                <img
                                    src="images/<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') ?>"
                                    loading="lazy"
                                >
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>

        <a class="back-link" href="index.php">Volver al inicio</a>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
