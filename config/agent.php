<?php

return [

    /* -----------------------------------------------------------------
     |  Detectors
     | -----------------------------------------------------------------
     */

    'detectors' => [
        'language' => [
            'driver' => Arcanedev\Agent\Detectors\LanguageDetector::class,
        ],

        'device' => [
            'driver' => Arcanedev\Agent\Detectors\DeviceDetector::class,
        ],
    ],

];
