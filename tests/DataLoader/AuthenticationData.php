<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AuthenticationDTO;

class AuthenticationData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'user' => '1',
            'username' => 'legacyuser',
            //legacyuserpass
            'passwordSha256' => '4e11be1dc9443935c7b452729ab681294b7cec9276d80dc72782221d68edb786',
            'passwordBcrypt' => null
        ];

        $arr[] = [
            'user' => '2',
            'username' => 'newuser',
            'passwordSha256' => null,
            //newuserpass
            'passwordBcrypt' => '$2a$10$CY96FLwV3dAA3hOLBHc/TeFZoaqH5/qlwNoFioj7dtKkKlVBTquve'
        ];
        return $arr;
    }

    public function create()
    {
        return [
            'user' => '3',
            'username' => 'createduser',
            'password' => 'newpass'
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function createMany($count)
    {
        throw new \Exception("Cannot auto create many Authentications.  Users have to be created first");
    }

    public function getDtoClass(): string
    {
        return AuthenticationDTO::class;
    }
}
