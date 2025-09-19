<?php

$factory1 = [
    "gio_binh_thuong" => [
        "khung_gio" => [
            ["04:00", "09:30", "thu_hai_den_thu_bay"],
            ["11:30", "17:00", "thu_hai_den_thu_bay"],
            ["20:00", "22:00", "thu_hai_den_thu_bay"],
            ["04:00", "22:00", "chu_nhat"]
        ],
        "don_gia" => 1896
    ],
    "gio_cao_diem" => [
        "khung_gio" => [
            ["09:30", "11:30", "thu_hai_den_thu_bay"],
            ["17:00", "20:00", "thu_hai_den_thu_bay"]
        ],
        "don_gia" => 3474
    ],
    "gio_thap_diem" => [
        "khung_gio" => [
            ["22:00", "04:00", "tat_ca_ngay"]
        ],
        "don_gia" => 1241
    ]
];

$factory2 = [
    "gio_binh_thuong" => [
        "khung_gio" => [
            ["04:00", "09:30", "thu_hai_den_thu_bay"],
            ["11:30", "17:00", "thu_hai_den_thu_bay"],
            ["20:00", "22:00", "thu_hai_den_thu_bay"],
            ["04:00", "22:00", "chu_nhat"]
        ],
        "don_gia" => 1749
    ],
    "gio_cao_diem" => [
        "khung_gio" => [
            ["09:30", "11:30", "thu_hai_den_thu_bay"],
            ["17:00", "20:00", "thu_hai_den_thu_bay"]
        ],
        "don_gia" => 3242
    ],
    "gio_thap_diem" => [
        "khung_gio" => [
            ["22:00", "04:00", "tat_ca_ngay"]
        ],
        "don_gia" => 1136
    ]
];

return [
    'factory1' => $factory1,
    'factory2' => $factory2,
];
