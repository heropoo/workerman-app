<?php
/**
 * Start A Http Server
 */

$root_path = dirname(dirname(__DIR__));
require_once $root_path . '/src/bootstrap.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Applications\HttpServer\HttpHandler;
use Workerman\Protocols\Http\Response;

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

//Worker::$daemonize = true;
//Worker::$stdoutFile = $root_path . '/runtime/logs/stdout.log';

Worker::$pidFile = $root_path . '/runtime/workerman-' . $worker->name . '.pid';

$handler = new HttpHandler();

$worker->onMessage = function (TcpConnection $connection, Request $request) use ($handler, $root_path) {
    echo '[' . date('Y-m-d H:i:s') . '] ' . $request->connection->getRemoteAddress() . ' '
        . $request->method() . ' ' . urldecode($request->uri());
    if ($request->uri() == '/favicon.ico') {
        $file = $root_path . '/public/favicon.ico';
        // 检查if-modified-since头判断文件是否修改过
        if (!empty($if_modified_since = $request->header('if-modified-since'))) {
            $modified_time = date('D, d M Y H:i:s', filemtime($file)) . ' ' . \date_default_timezone_get();
            // 文件未修改则返回304
            if ($modified_time === $if_modified_since) {
                $connection->send(new Response(304));
                return;
            }
        }
        // 文件修改过或者没有if-modified-since头则发送文件
        $response = (new Response())->withFile($root_path . '/public/favicon.ico');
    } else {
        /** @var Response $response */
        $response = $handler->handle($request);
    }

    echo ' ' . strstr($response->__toString(), PHP_EOL, true) . PHP_EOL;
    //var_dump($request->connection->getRemoteIp());
//    var_dump($request->sessionId());
    //var_dump($response);
//    var_dump($request->method());
//    var_dump($request->path());

//    var_dump($request->header());
    // 已经处理请求数
    //static $request_count = 0;

    // 向浏览器发送hello world
    //$connection->send('hello world');$connection
    $connection->send($response);
};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
