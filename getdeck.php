
<?php

function getDeck()
{
    $deck = [];

    $suits = [
        'corazones' => 'cor',
        'treboles' => 'tre',
        'picas' => 'pic',
        'rombos' => 'rom'
    ];

    foreach ($suits as $suit => $prefix) {
        for ($value = 1; $value <= 13; $value++) {
            if ($value === 11) {
                $imageValue = 'j';
            } elseif ($value === 12) {
                $imageValue = 'q';
            } elseif ($value === 13) {
                $imageValue = 'k';
            } else {
                $imageValue = (string) $value;
            }

            $deck[] = [
                'suit' => $suit,
                'value' => $value,
                'image' => $prefix . '_' . $imageValue . '.png'
            ];
        }
    }

    $deck[] = [
        'suit' => 'comodin',
        'value' => 'joker',
        'image' => 'jok_1.png'
    ];

    $deck[] = [
        'suit' => 'comodin',
        'value' => 'joker',
        'image' => 'jok_2.png'
    ];

    return $deck;
}
