<?php


namespace Applications\HttpServer;
use Moon\Routing\Route;
use Workerman\Protocols\Http\Request;
use Moon\Routing\Router;


class HttpHandler
{
    public function __construct()
    {
//        $route_config = config('route');
//        $router = new Router([
//            'namespace' => isset($route_config['namespace']) ? $route_config['namespace'] : 'App\Controllers',
//            'prefix' => isset($route_config['prefix']) ? $route_config['prefix'] : null,
//            'middleware' => isset($route_config['middleware']) ? $route_config['middleware'] : [],
//        ]);
    }

    public function handle(Request $request){
        $container = \App::$container;

        $router = new Router();
        require_once __DIR__.'/routes.php';

        $matchResult = $router->dispatch($request->path(), $request->method());

        /** @var Route $route */
        $route = $matchResult['route'];
        $params = $matchResult['params'];

        $params = array_map(function ($param) {
            return urldecode($param);
        }, $params);

        $middlewareList = $route->getMiddleware();

        $result = $this->filterMiddleware($request, $middlewareList);

        if (!is_null($result)) {
            return $this->makeResponse($result);
        }

        try {
             // resolve controller
            $action = $route->getAction();
            if ($action instanceof \Closure) {
                $data = $container->callFunction($action, $params);
                return $this->makeResponse($data);
            } else {
                $actionArr = explode('::', $action);
                $controllerName = $actionArr[0];
                if (!class_exists($controllerName)) {
                    throw new \Exception("Controller class '$controllerName' is not exists!");
                }
                $methodName = isset($actionArr[1]) ? $actionArr[1] : null;
                $data = $container->callMethod($controllerName, $methodName, $params);
                return $this->makeResponse($data);
            }
        } catch (HttpException $e) {
            return $this->makeResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param array $middlewareList
     * @return mixed
     * @throws \Exception
     */
    protected function filterMiddleware($request, $middlewareList)
    {
        if (empty($middlewareList)) {
            return null;
        }
        $middleware = array_shift($middlewareList);
        if (!class_exists($middleware)) {
            throw new \Exception('Class ' . $middleware . ' is not exists!');
        }
        $middlewareObj = new $middleware();
        return $middlewareObj->handle($request, function ($request) use ($middlewareList) {
            return $this->filterMiddleware($request, $middlewareList);
        });
    }

    /**
     * @param mixed $data
     * @param int $status
     * @return JsonResponse|Response
     */
    protected function makeResponse($data, $status = 200)
    {
        if ($data instanceof Response) {
            return $data;
        } else if ($data instanceof View) {
            return new Response(strval($data), $status);
        } else if (is_array($data) || is_object($data)) {
            return new JsonResponse($data, $status);
        } else {
            return new Response(strval($data), $status);
        }
    }
}