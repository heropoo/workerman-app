<?php


namespace Moon;

use Moon\Container\Container;
use Moon\HttpException;
use Moon\Routing\Route;
use Moon\Routing\UrlMatchException;
use Workerman\Protocols\Http\Request;
use Moon\Routing\Router;
use Workerman\Protocols\Http\Response;


class HttpHandler
{
    protected $router;
    protected $container;

    public function __construct(Container $container, Router $router, $routes_file)
    {
        $this->container = $container;
        $this->router = $router;

        require_once $routes_file;
    }

    public function handle(Request $request)
    {
        $router = $this->router;
        $container = $this->container;

        $container->add(Request::class, $request);
        try {
            $matchResult = $router->dispatch($request->path(), $request->method());

            /** @var Route $route */
            $route = $matchResult['route'];
            $params = $matchResult['params'];

            $request->route = $route;

            $params = array_map(function ($param) {
                return urldecode($param);
            }, $params);

            $middlewareList = $route->getMiddleware();

            $result = $this->filterMiddleware($request, $middlewareList);

            if (!is_null($result)) {
                return $this->makeResponse($result);
            }

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
        } catch (UrlMatchException $e) {
            return $this->makeResponse($e->getMessage(), $e->getCode());
        } catch (HttpException $e) { //todo
            return $this->makeResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param array $middlewares
     * @return mixed
     * @throws \Exception
     */
    protected function filterMiddleware(Request $request, array $middlewares)
    {
        if (empty($middlewares)) {
            return null;
        }
        $middleware = array_shift($middlewares);
        if (!class_exists($middleware)) {
            throw new \Exception('Class ' . $middleware . ' is not exists!');
        }
        $middlewareObj = new $middleware();
        return $middlewareObj->handle($request, function ($request) use ($middlewares) {
            return $this->filterMiddleware($request, $middlewares);
        });
    }

    /**
     * @param mixed $data
     * @param int $status
     * @return string
     */
    protected function makeResponse($data, $status = 200)
    {
        if ($data instanceof Response) {
            return $data;
        }
        //todo View
//        else if ($data instanceof View) {
//            return new Response(strval($data), $status);
//        }// else
        else if (is_array($data) || is_object($data)) {
            return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
        } else {
            return new Response($status, ['Content-Type' => 'text/html;charset=' . \App::$instance->getCharset()], $data);
        }
    }
}