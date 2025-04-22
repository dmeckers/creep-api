<?php

declare(strict_types=1);

/**
 * @todo Move config to .env file
 */
return [
    'docker' => [
        'image' => 'dmitriymecker/cream-fm:latest',
        'shared-network' => 'killme_internal',
        'memory-limit' => '512m',
        'host-config-mount-path' => '/Users/dmitrijsmeckers/Desktop/killme/liq/configs'
    ]
];
