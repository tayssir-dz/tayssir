<?php

return [
    'widgets' => [
        'stats' => [
            'total_activations' => 'إجمالي التفعيلات',
            'unique_students' => 'عدد الطلاب الفريدين',
            'total_discount' => 'إجمالي الخصومات المقدمة',
            'total_margin' => 'إجمالي الهامش المكتسب',
        ],
        'most_used' => [
            'heading' => 'أكثر كود خصم استخدامًا',
            'columns' => [
                'code' => 'كود الخصم',
                'activations' => 'عدد التفعيلات',
                'total_discount' => 'إجمالي الخصم',
                'total_margin' => 'إجمالي الهامش',
            ],
            'empty' => [
                'title' => 'لا توجد تفعيلات بعد',
                'description' => 'عند استخدام أكوادك سيظهر الأفضل أداءً هنا.',
            ],
        ],
    ],
];
