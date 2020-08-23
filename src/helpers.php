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