<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AuthenticationDTO;
use DateTime;
use Exception;

final class AuthenticationData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'user' => 1,
            'username' => 'legacyuser',
            'password' => 'legacyuserpass',
        ];

        $arr[] = [
            'user' => 2,
            'username' => 'newuser',
            'password' => 'newuserpass',
        ];

        $arr[] = [
            'user' => 3,
            'username' => 'secureuser',
            'password' => 'pass',
            'invalidateTokenIssuedBefore' => new DateTime(),
        ];
        return $arr;
    }

    public function create(): array
    {
        return [
            'user' => 4,
            'username' => 'createduser',
            'password' => 'newpass',
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function createMany(int $count): array
    {
        throw new Exception("Cannot auto create many Authentications.  Users have to be created first");
    }

    public function getDtoClass(): string
    {
        return AuthenticationDTO::class;
    }

    /**
     * Overwrite this so that password will be included in the request when it is set
     * even though password isn't an exposed property of the DTO
     */
    protected function buildJsonApiObject(array $arr, string $dtoClass): array
    {
        $rhett = parent::buildJsonApiObject($arr, $dtoClass);
        if (array_key_exists('password', $arr)) {
            $rhett['attributes']['password'] = $arr['password'];
        }
        return $rhett;
    }
}
