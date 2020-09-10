<?php


if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }
        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * get a config
     * @param string $key
     * @param bool $throw
     * @return mixed|null|\Moon\Config\Exception
     */
    function config($key, $throw = false)
    {
        /** @var \Moon\Config\Config $config */
        $config = \App::$container->get('config');  //todo update config component
        return $config->get($key, $throw);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////

function jsonRpcError($code, $message, $id = null)
{
    return [
        'jsonrpc' => '2.0',
        'error' => [
            'code' => $code,
            'message' => $message,
        ],
        'id' => $id
    ];
}


function jsonRpcResult($result, $id = null)
{
    return [
        'jsonrpc' => '2.0',
        'result' => $result,
        'id' => $id
    ];
}

/**
 * 下划线转驼峰
 * @param string $str
 * @return string
 */
function toCamelCase(string $str): string
{
    $words = '_' . str_replace('_', " ", strtolower($str));
    return ltrim(str_replace(" ", "", ucwords($words)), '_');
}

/**
 * 驼峰命名转下划线命名
 * @param string $str
 * @return string
 */
function toUnderScore(string $str): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . '_' . "$2", $str));
}