<?php

return [
    'tinyint' => [
        'signed' => [
            'min' => -128,
            'max' => 127
        ],
        'unsigned' => [
            'min' => 0,
            'max' => 255
        ]
    ],
    'smallint' => [
        'signed' => [
            'min' => -32768,
            'max' => 32767
        ],
        'unsigned' => [
            'min' => 0,
            'max' => 65535
        ]
    ],
    'mediumint' => [
        'signed' => [
            'min' => -8388608,
            'max' => 8388607
        ],
        'unsigned' => [
            'min' => 0,
            'max' => 16777215
        ]
    ],
    'int' => [
        'signed' => [
            'min' => -2147483648,
            'max' => 2147483647
        ],
        'unsigned' => [
            'min' => 0,
            'max' => 4294967295
        ]
    ],
    'bigint' => [
        'signed' => [
            'min' => -9223372036854775808,
            'max' => 9223372036854775807
        ],
        'unsigned' => [
            'min' => 0,
            'max' => 18446744073709551615
        ]
    ]
];
