<?php

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