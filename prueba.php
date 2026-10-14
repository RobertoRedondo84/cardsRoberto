
<?php

require_once 'getdeck.php';

$deck = getDeck();

echo '<h1>Prueba de la baraja</h1>';

echo '<p>Total de cartas: ' . count($deck) . '</p>';

echo '<h2>Primeras cartas</h2>';

echo '<pre>';
print_r(array_slice($deck, 0, 5));
echo '</pre>';

echo '<h2>Últimas cartas</h2>';

echo '<pre>';
print_r(array_slice($deck, -2));
echo '</pre>';

?>
