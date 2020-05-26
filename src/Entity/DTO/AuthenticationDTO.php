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
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("string")
    */
    public $user;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
    */
    public $username;

    public function __construct(
        $user,
        $username
    ) {
        $this->user = $user;
        $this->username = $username;
    }
}
