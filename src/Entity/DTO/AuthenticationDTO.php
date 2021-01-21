<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class Authentication
 *
 * @IS\DTO("authentications")
 */
class AuthenticationDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("string")
    */
    public int $user;

    /**
     * @IS\Expose
     * @IS\Type("string")
     *
    */
    public string $username;

    public function __construct(
        int $user,
        string $username
    ) {
        $this->user = $user;
        $this->username = $username;
    }
}
