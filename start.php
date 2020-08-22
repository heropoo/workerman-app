<?php

ini_set('display_errors', 'on');
use Workerman\Worker;

// 标记是全局启动
define('GLOBAL_START', 1);

require_once __DIR__ . '/vendor/autoload.php';

// var_dump(glob(__DIR__ . '/Applications/*/start*.php'));exit;

// app/*/start.php，以便启动所有服务
foreach(glob(__DIR__ . '/Applications/*/start*.php') as $start_file)
{
    require_once $start_file;
}

// 运行所有服务
Worker::runAll();
