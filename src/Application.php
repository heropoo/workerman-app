<?php
use Moon\Container\Container;
use Moon\Config\Config;
use Dotenv\Exception\ExceptionInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Application
{
    /** @var Container $container */
    public $container;

    protected $config = [];
    protected $rootPath;

    protected $environment = 'production';
    protected $debug = false;
    protected $charset = 'UTF-8';
    protected $timezone = 'UTC';

    public function __construct($rootPath, array $options = [], Container $container = null)
    {
        if (!is_dir($rootPath)) {
            throw new Exception("Directory '$rootPath' is not exists!");
        }
        $this->rootPath = realpath($rootPath);
        foreach ($options as $option => $value) {
            $this->$option = $value;
        }
        $this->container = is_null($container) ? new Container() : $container;

        \App::$instance = $this;
        \App::$container = $this->container;

        $this->init();
    }

    protected function init(){
        try {
            (new \Dotenv\Dotenv($this->rootPath))->load();
        } catch (ExceptionInterface $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        $config = new Config($this->rootPath.'/config');
        $this->container->add('config', $config);

        $this->config = $config->get('app', true);

        if (isset($this->config['timezone'])) {
            $this->timezone = $this->config['timezone'];
            date_default_timezone_set($this->timezone);
        }

        if (isset($this->config['charset'])) {
            $this->charset = $this->config['charset'];
        }

        if (isset($this->config['environment'])) {
            $this->environment = $this->config['environment'];
        }

        if (isset($this->config['debug'])) {
            $this->debug = (bool)$this->config['debug'];
        }

        $logger = new Logger('app');
        $this->container->add('logger', $logger);
        $logger->pushHandler(new StreamHandler(
            $this->rootPath . '/runtime/logs/app-' . date('Y-m-d') . '.log', Logger::DEBUG
        ));

        $this->initComponents();
    }

    public function initComponents()
    {
        isset($this->config['components']) ?: $this->config['components'] = [];
        foreach ($this->config['components'] as $componentName => $params) {

            $className = $params['class'];
            unset($params['class']);

            if (!isset($params['auto_inject_by_class']) || $params['auto_inject_by_class'] !== false) {
                $this->container->alias($className, $componentName);
            }
            unset($params['auto_inject_by_class']);

            $this->container->bind($componentName, function () use ($className, $params) {
                $ref = new \ReflectionClass($className);
                return $ref->newInstanceArgs($params);
            }, true);

        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'get') === 0) { //get protected attribute
            $attribute = lcfirst(substr($name, 3));
            if (isset($this->$attribute)) {
                return $this->$attribute;
            }
        }
        throw new Exception('Call to undefined method ' . get_class($this) . '::' . $name . '()');
    }
}