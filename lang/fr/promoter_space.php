<?php

return [
    'widgets' => [
        'stats' => [
            'total_activations' => 'Activations totales',
            'unique_students' => 'Étudiants uniques',
            'total_discount' => 'Réduction totale accordée',
            'total_margin' => 'Marge totale gagnée',
        ],
        'most_used' => [
            'heading' => 'Code promo le plus utilisé',
            'columns' => [
                'code' => 'Code promo',
                'activations' => 'Activations',
                'total_discount' => 'Réduction totale',
                'total_margin' => 'Marge totale',
            ],
            'empty' => [
                'title' => "Aucune activation pour l'instant",
                'description' => 'Dès que vos codes promo sont utilisés, le meilleur apparaîtra ici.',
            ],
        ],
    ],
];
