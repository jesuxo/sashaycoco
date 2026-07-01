<?php

return [
    'compresion' => [
        // Calidad para JPEG (0-100)
        'calidad_jpeg' => env('IMG_QUALITY_JPEG', 75),

        // Compresión para PNG (0-9, 0 = sin compresión, 9 = máxima)
        'compresion_png' => env('IMG_COMPRESSION_PNG', 8),

        // Tamaño máximo en píxeles (el lado más largo)
        'max_lado' => env('IMG_MAX_SIZE', 1920),

        // Tamaño máximo en MB antes de comprimir
        'max_mb' => env('IMG_MAX_MB', 5),
    ],
];
