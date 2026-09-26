<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

const APP_VERSION = 'v2.1.3';

return App::config([
    'parameters' => [
        'app.version' => substr(APP_VERSION, 1),
    ],
    'services' => [
        '_defaults' => [
            'autowire' => true,
            'autoconfigure' => true,
        ],
        'App\\' => [
            'resource' => '../src/',
        ],
    ],
]);
