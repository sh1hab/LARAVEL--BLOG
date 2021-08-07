<?php

namespace App\Enums;

final class UserTypes
{
    const user = 'user';
    const manager = 'manager';
    const admin = 'admin';

    public static function getUserTypesArray()
    {
        return [
            self::user,
            self::manager,
            self::admin
        ];
    }
}
