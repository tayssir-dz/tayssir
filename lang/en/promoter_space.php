<?php

return [
    'widgets' => [
        'stats' => [
            'total_activations' => 'Total activations',
            'unique_students' => 'Unique students',
            'total_discount' => 'Total discount given',
            'total_margin' => 'Total margin earned',
        ],
        'most_used' => [
            'heading' => 'Most used promo code',
            'columns' => [
                'code' => 'Promo code',
                'activations' => 'Activations',
                'total_discount' => 'Total discount',
                'total_margin' => 'Total margin',
            ],
            'empty' => [
                'title' => 'No activations yet',
                'description' => 'Once your promo codes are used, you will see the top performer here.',
            ],
        ],
    ],
];
