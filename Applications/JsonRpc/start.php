<?php

$root_path = dirname(dirname(__DIR__));
require_once $root_path . '/src/bootstrap.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

// 自动加载类
//require_once __DIR__ . '/Clients/StatisticClient.php';
//require_once __DIR__ . '/../../JsonNL.php';

// 开启的端口
$worker = new Worker('JsonNL://0.0.0.0:2015');
// 启动多少服务进程
$worker->count = 16;
// worker名称，php start.php status 时展示使用
$worker->name = 'JsonRpc';

$namespace = '\\Applications\\JsonRpc\\Services';

$worker->onMessage = function (TcpConnection $connection, $data) use ($namespace) {
    $statistic_address = 'udp://127.0.0.1:55656';
    var_dump($data);

    if (!empty($data['exit'])) {
        $connection->destroy();
    }

    $id = $data['id'] ?? null;

    // 判断数据是否正确
    if (empty($data['method'])) {
        // 发送数据给客户端，请求包错误
//        return $connection->send(array('code' => 400, 'msg' => 'bad request', 'data' => null));
        return $connection->send(jsonRpcError(-32600, 'Invalid Request', $id));
    }
    // 获得要调用的类、方法、及参数
    $method = $data['method']; // {"method": "test-user/hello", "id": 123}
    $params = $data['params'] ?? [];

    $arr = explode('::', $method);
    $class_name = $namespace . '\\' . $arr[0];
    $action_name = $arr[1] ?? 'index';
    var_dump($class_name, $action_name);
    if (!class_exists($class_name) || !method_exists($class_name, $action_name)) {
        return jsonRpcError(-32601, 'Method not found', $id);
    }

    try {
        $result = call_user_func_array(array($class_name, $action_name), $params);
        return $connection->send(jsonRpcResult($result, $id));
    } catch (Exception $e) {
        return $connection->send(jsonRpcError(-32603, 'Internal error', $id));
    }

};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
