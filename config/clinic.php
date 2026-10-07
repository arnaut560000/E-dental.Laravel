<?php

return [
    'name' => env('CLINIC_NAME', 'Talavera E-dental'),
    'privacy_contact' => env('PRIVACY_CONTACT', 'the clinic administrator'),
    'privacy_notice_version' => '2026-09-22',

    // Carbon: 0 = Sunday ... 6 = Saturday (Mon/Wed/Fri = 1,3,5)
    'days' => [1, 3, 5],
    'opening_time' => '08:00',
    'closing_time' => '12:00',
    'slot_minutes' => 15,

    'max_public_requests_per_day' => 50,
    'request_cooldown_days' => 30,
    'max_id_document_kb' => 10240,

    'categories' => ['General Public', 'PWD', 'Senior Citizen', 'Pregnant Woman'],
    'id_required_categories' => ['PWD', 'Senior Citizen'],
    'genders' => ['Female', 'Male', 'Others'],
    'registration_modes' => ['Email', 'Form', 'Online', 'Text', 'Walk-in'],

    'barangays' => [
        'Andal Alino (Pob.)', 'Bagong Sikat', 'Bagong silang', 'Bakal I', 'Bakal II', 'Bakal III',
        'Baluga', 'Bantug', 'Bantug Hacienda', 'Basang Hamog', 'Bugtong na Buli', 'Bulac',
        'Caaniplahan', 'Cabubulaunan', 'Calipahan', 'Campos', 'Caputican', 'Casulucan Este',
        'Collado', 'Dimasalang Norte', 'Dimasalang Sur', 'Dinarayat', 'Esguerra District (Pob.)',
        'Gulod', 'Homestead I', 'Homestead II', 'Kinalanguyan', 'LaTorre', 'Lomboy', 'Mabuhay',
        'Maestrang Kikay', 'Mamandil', 'Marcos District', 'Matiass', 'Matingkis', 'Minabuyoc',
        'Pag-Asa', 'Paludpod', 'Pantoc Bulac', 'Pinagpanaan', 'Poblacion Sur', 'Pula',
        'Pulong San Miguel', 'Sampaloc', 'San Miguel Na Munti', 'San Pascual', 'San Ricardo',
        'Sibul', 'Sicsican Matanda', 'Tabacao', 'Tagaytay', 'Valle', 'Iba (kung taga ibang bayan)',
    ],
];