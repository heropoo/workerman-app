<?php

return [
    'timezone' => 'Asia/Shanghai',
    'components' => [
        'redis' => [
            'class' => 'Moon\Cache\Redis',
            'host' => env('REDIS_HOST', 'localhost'),
            'port' => env('REDIS_PORT', 6379),
            'password' => env('REDIS_PASSWORD'),
            'database' => env('REDIS_DATABASE', 0)
        ],
        'db' => [
            'class' => 'Moon\Db\Connection',
            //'auto_inject_by_class'=> true, // default true
            'master' => [
                'dsn' => 'mysql:host=' . env('DB_HOST', 'localhost')
                    . ';dbname=' . env('DB_NAME', 'test') . ';port=' . env('DB_PORT', '3306'),
                'username' => env('DB_USER', 'root'),
                'password' => env('DB_PWD', ''),
                'charset' => 'utf8mb4',
                //'tablePrefix' => 'tt_',
                'emulatePrepares' => false,
                'options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            ]
        ]
    ]
];