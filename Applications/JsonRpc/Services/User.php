<?php


namespace Applications\JsonRpc\Services;


class User
{
    // {"method": "User::getInfo", "params": [100]}
    public static function getInfo($id)
    {
        return [
            'name' => 'xiao' . mt_rand(1, 9),
            'id' => $id
        ];
    }
}