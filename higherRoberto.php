<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/getdeck.php';

function resolveHigherRound(array &$game): void
{
    $round = $game['round'];
    $value1 = $game['player1'][$round]['value'];
    $value2 = $game['player2'][$round]['value'];
    $rank1 = $value1 === 'joker' ? 14 : (int) $value1;
    $rank2 = $value2 === 'joker' ? 14 : (int) $value2;

    if ($rank1 > $rank2) {
        $game['score1']++;
        $game['round_result'] = 'Gana el jugador 1 esta mano.';
    } elseif ($rank1 < $rank2) {
        $game['score2']++;
        $game['round_result'] = 'Gana el jugador 2 esta mano.';
    } else {
        $game['round_result'] = 'Esta mano termina en empate.';
    }

    if ($game['round'] === 9) {
        if ($game['score1'] > $game['score2']) {
            $game['result'] = '¡Gana el jugador 1!';
            $game['result_class'] = 'player-one';
        } elseif ($game['score1'] < $game['score2']) {
            $game['result'] = '¡Gana el jugador 2!';
            $game['result_class'] = 'player-two';
        } else {
            $game['result'] = '¡Empate!';
            $game['result_class'] = 'draw';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game = $_SESSION['higher_game'] ?? null;
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_array($game)
        || !isset($game['csrf_token'])
        || !is_string($csrfToken)
        || !hash_equals($game['csrf_token'], $csrfToken)
        || $game['round'] >= 9
    ) {
        http_response_code(400);
        exit('No se pudo avanzar la partida. Inicia una nueva partida e inténtalo otra vez.');
    }

    $game['round']++;
    resolveHigherRound($game);
    $_SESSION['higher_game'] = $game;

    header('Location: higherRoberto.php');
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('Método de petición no permitido.');
}

if (
    ($_GET['new'] ?? '') === '1'
    || !isset($_SESSION['higher_game'])
) {
    $deck = getDeck();
    shuffle($deck);
    $player1 = [];
    $player2 = [];

    for ($i = 0; $i < 10; $i++) {
        $player1[] = array_pop($deck);
        $player2[] = array_pop($deck);
    }

    $game = [
        'player1' => $player1,
        'player2' => $player2,
        'round' => 0,
        'score1' => 0,
        'score2' => 0,
        'round_result' => '',
        'csrf_token' => bin2hex(random_bytes(32)),
    ];
    resolveHigherRound($game);
    $_SESSION['higher_game'] = $game;
} else {
    $game = $_SESSION['higher_game'];
}
?>
<!-- html del juego Higher -->
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
            <p>Diez manos: gana quien consiga más cartas de valor superior.</p>
        </div>

        <p class="higher-round-number">Mano <?= $game['round'] + 1 ?> de 10</p>

        <section class="scoreboard" aria-label="Resultado de la partida">
            <div class="score">
                <span>Jugador 1</span>
                <strong><?= $game['score1'] ?></strong>
                <span class="score-caption">manos ganadas</span>
            </div>
            <div class="score-divider" aria-hidden="true">VS</div>
            <div class="score">
                <span>Jugador 2</span>
                <strong><?= $game['score2'] ?></strong>
                <span class="score-caption">manos ganadas</span>
            </div>
            <p class="game-result">
                <?= htmlspecialchars($game['round_result'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </section>

        <div class="players higher-round">
            <?php foreach ([
                ['name' => 'Jugador 1', 'card' => $game['player1'][$game['round']]],
                ['name' => 'Jugador 2', 'card' => $game['player2'][$game['round']]],
            ] as $player): ?>
                <?php
                $suit = (string) $player['card']['suit'];
                $value = (string) $player['card']['value'];
                $alt = $suit . ' ' . $value;
                ?>
                <section class="player-panel">
                    <h2><?= htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <ul class="card-list">
                        <li>
                            <img
                                src="images/<?= htmlspecialchars($player['card']['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </li>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="higher-actions">
            <?php if ($game['round'] < 9): ?>
                <form action="higherRoberto.php" method="post">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($game['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <button type="submit">Siguiente mano</button>
                </form>
            <?php else: ?>
                <p class="game-result <?= htmlspecialchars($game['result_class'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($game['result'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
            <a class="back-link" href="higherRoberto.php?new=1">Nueva partida</a>
            <a class="back-link" href="index.php">Volver al inicio</a>
        </div>

        <?php if ($game['round'] === 9): ?>
            <section class="higher-summary" aria-labelledby="higher-summary-title">
                <h2 id="higher-summary-title">Resumen de las diez manos</h2>
                <div class="higher-summary-grid">
                    <?php for ($round = 0; $round < 10; $round++): ?>
                        <?php
                        $card1 = $game['player1'][$round];
                        $card2 = $game['player2'][$round];
                        $value1 = $card1['value'] === 'joker' ? 14 : (int) $card1['value'];
                        $value2 = $card2['value'] === 'joker' ? 14 : (int) $card2['value'];
                        if ($value1 > $value2) {
                            $roundWinner = 'Jugador 1';
                        } elseif ($value1 < $value2) {
                            $roundWinner = 'Jugador 2';
                        } else {
                            $roundWinner = 'Empate';
                        }
                        ?>
                        <article class="higher-summary-hand">
                            <h3>Mano <?= $round + 1 ?></h3>
                            <div class="higher-summary-cards">
                                <?php foreach ([$card1, $card2] as $card): ?>
                                    <?php
                                    $alt = (string) $card['suit'] . ' ' . (string) $card['value'];
                                    ?>
                                    <img
                                        src="images/<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') ?>"
                                        loading="lazy"
                                    >
                                <?php endforeach; ?>
                            </div>
                            <p><?= htmlspecialchars($roundWinner, ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endfor; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
