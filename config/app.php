<?php

return [
    'components' => [
        'log' => [

        ],
        'db' => [
            'class' => \Moon\Db\Connection::class,
            'master' => [
                'dsn' => 'mysql:host=localhost;dbname=test;port=3306',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                //'tablePrefix' => 'tt_',
                'emulatePrepares' => false,
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            ]
        ],
    ]
];