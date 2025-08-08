<?php

return [
    (new FoF\Redis\Extend\Redis([
        'host' => 'redis',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ]))->disable(['queue'])
];