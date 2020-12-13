<?php


namespace Applications\JsonRpc\Services;


class User
{
    // {"method": "User::getInfo", "params": [1]}
    public static function getInfoAction($id)
    {
        $users = [
            1 => ['id' => 1, 'name' => '小明', 'sex' => '男'],
            2 => ['id' => 2, 'name' => '小萌', 'sex' => '女'],
        ];

        $user = $users[$id];

        return [
            'user' => $user
        ];
    }
}