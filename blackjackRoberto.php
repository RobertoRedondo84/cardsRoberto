<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/getdeck.php';

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

function resolveBlackjackRound(array &$game): void
{
    $playerName = 'Jugador ' . ($game['round'] + 1);
    $playerScore = calculateScore($game['participants'][$playerName]);
    $bankScore = $game['bank_score'];

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

    $game['results'][$playerName] = [
        'score' => $playerScore,
        'result' => $result,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game = $_SESSION['blackjack_game'] ?? null;
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_array($game)
        || !isset($game['csrf_token'], $game['round'], $game['participants'], $game['results'])
        || !is_string($csrfToken)
        || !hash_equals($game['csrf_token'], $csrfToken)
        || $game['round'] >= 4
    ) {
        http_response_code(400);
        exit('No se pudo avanzar la partida. Inicia una nueva partida e inténtalo otra vez.');
    }

    $game['round']++;
    resolveBlackjackRound($game);
    $_SESSION['blackjack_game'] = $game;

    header('Location: blackjackRoberto.php');
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('Método de petición no permitido.');
}

if (
    ($_GET['new'] ?? '') === '1'
    || !isset($_SESSION['blackjack_game'])
) {
    $deck = getDeck();
    array_pop($deck);
    array_pop($deck);
    shuffle($deck);

    $participants = [
        'Banca' => [],
        'Jugador 1' => [],
        'Jugador 2' => [],
        'Jugador 3' => [],
        'Jugador 4' => [],
        'Jugador 5' => [],
    ];

    for ($round = 0; $round < 2; $round++) {
        foreach ($participants as $name => $cards) {
            $participants[$name][] = array_pop($deck);
        }
    }

    foreach ($participants as $name => $cards) {
        while (calculateScore($participants[$name]) < 14) {
            $participants[$name][] = array_pop($deck);
        }
    }

    $game = [
        'participants' => $participants,
        'bank_score' => calculateScore($participants['Banca']),
        'round' => 0,
        'results' => [],
        'csrf_token' => bin2hex(random_bytes(32)),
    ];
    resolveBlackjackRound($game);
    $_SESSION['blackjack_game'] = $game;
} else {
    $game = $_SESSION['blackjack_game'];
}

$currentPlayer = 'Jugador ' . ($game['round'] + 1);
$currentResult = $game['results'][$currentPlayer];
?>
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
            <p>Cinco manos frente a la banca. Se muestra una mano cada vez.</p>
        </header>

        <p class="blackjack-round-number">
            Mano <?= $game['round'] + 1 ?> de 5
        </p>

        <section class="blackjack-bank">
            <h2>Banca</h2>
            <p class="blackjack-score">
                Puntuación: <strong><?= $game['bank_score'] ?></strong>
            </p>
            <ul class="card-list">
                <?php foreach ($game['participants']['Banca'] as $card): ?>
                    <li>
                        <img
                            src="images/<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($card['suit'] . ' ' . $card['value'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="blackjack-players">
            <h2>Mano actual</h2>
            <article class="blackjack-player">
                <h3><?= htmlspecialchars($currentPlayer, ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="blackjack-score">
                    Puntuación: <strong><?= $currentResult['score'] ?></strong>
                </p>
                <p class="blackjack-result">
                    Resultado: <strong><?= htmlspecialchars($currentResult['result'], ENT_QUOTES, 'UTF-8') ?></strong>
                </p>
                <ul class="card-list">
                    <?php foreach ($game['participants'][$currentPlayer] as $card): ?>
                        <li>
                            <img
                                src="images/<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($card['suit'] . ' ' . $card['value'], ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </section>

        <nav class="blackjack-actions" aria-label="Acciones de la partida">
            <?php if ($game['round'] < 4): ?>
                <form action="blackjackRoberto.php" method="post">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($game['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <button type="submit">Siguiente mano</button>
                </form>
            <?php else: ?>
                <p class="blackjack-final-result">Fin de la partida</p>
            <?php endif; ?>
            <a class="back-link" href="blackjackRoberto.php?new=1">Nueva partida</a>
            <a class="back-link" href="index.php">Volver al inicio</a>
        </nav>

        <?php if ($game['round'] === 4): ?>
            <section class="blackjack-summary" aria-labelledby="blackjack-summary-title">
                <h2 id="blackjack-summary-title">Resumen de las cinco manos</h2>
                <div class="blackjack-summary-grid">
                    <?php foreach ($game['results'] as $name => $result): ?>
                        <article class="blackjack-summary-hand">
                            <h3><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="blackjack-score">
                                Puntuación: <strong><?= $result['score'] ?></strong>
                            </p>
                            <p class="blackjack-result">
                                Resultado: <strong><?= htmlspecialchars($result['result'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </p>
                            <ul class="card-list">
                                <?php foreach ($game['participants'][$name] as $card): ?>
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
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
