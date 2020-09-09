<?php
/**
 * Start A Http Server
 */

require_once ROOT_PATH.'/src/bootstrap.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Applications\HttpServer\HttpHandler;

// 自动加载类
//require_once __DIR__ . '/Clients/StatisticClient.php';
//require_once __DIR__ . '/../../JsonNL.php';

// 每个进程最多执行1000个请求
//define('MAX_REQUEST', 1000);

// 开启的端口
$worker = new Worker('http://0.0.0.0:2345');
// 启动多少服务进程
$worker->count = 16;
// worker名称，php start.php status 时展示使用
$worker->name = 'HttpServer';

$handler = new HttpHandler();

$worker->onMessage = function (TcpConnection $connection, Request $request) use($handler) {
    $response = $handler->handle($request);
    var_dump($response);
//    var_dump($request->method());
//    var_dump($request->path());

//    var_dump($request->header());
    // 已经处理请求数
    //static $request_count = 0;

    // 向浏览器发送hello world
    $connection->send('hello world');
};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
