<?php

return [
    'cities' => [
        'الرياض', 'جدة', 'الدمام', 'مكة', 'المدينة', 'أبها', 'الطائف'
    ],

    'amenities' => [
        'مسبح', 'صالة رياضية', 'مصعد', 'حراسة', 'موقف سيارات', 
        'حديقة', 'غرفة خادم', 'غرفة دراسة', 'غرفة ألعاب'
    ],

    'types' => [
        'residential' => ['apartment', 'villa', 'townhouse', 'penthouse'],
        'commercial' => ['office', 'showroom', 'shop', 'building'],
        'industrial' => ['warehouse', 'factory', 'land'],
        'land' => ['residential_land', 'commercial_land', 'agricultural_land']
    ],

    'statuses' => [
        'draft' => 'مسودة',
        'pending_review' => 'قيد المراجعة',
        'approved' => 'موافق عليه',
        'active' => 'نشط',
        'under_offer' => 'تحت العرض',
        'reserved' => 'محجوز',
        'sold' => 'تم البيع',
        'rented' => 'تم الإيجار',
        'archived' => 'مؤرشف'
    ],

    'commission' => [
        'default_percentage' => 2.5,
        'min_percentage' => 1,
        'max_percentage' => 5
    ]
];