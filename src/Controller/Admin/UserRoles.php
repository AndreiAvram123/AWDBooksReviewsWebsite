<?php

namespace App\Controller\Admin;

use JetBrains\PhpStorm\ArrayShape;

class UserRoles
{
    #[ArrayShape(["ROLE_ADMIN" => "string", "ROLE_MODERATOR" => "string", "ROLE_USER" => "string"])] static function provideFormUserRoles():array{
        return [
            "ROLE_ADMIN" => "ROLE_ADMIN",
            "ROLE_MODERATOR" =>"ROLE_MODERATOR",
            "ROLE_USER" => "ROLE_USER"
        ];
    }
}