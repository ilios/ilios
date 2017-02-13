<?php
namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class Authentication
 * @package Ilios\CoreBundle\Entity\DTO
 *
 */
class AuthenticationDTO
{
    /**
     * @var int
     *
     * @IS\Type("string")
    */
    public $user;

    /**
     * @var string
     * @IS\Type("string")
     *
    */
    public $username;

    /**
     * @IS\Type("dateTime")
     */
    protected $invalidateTokenIssuedBefore;

    public function __construct(
        $user,
        $username
    ) {
        $this->user = $user;
        $this->username = $username;
    }
}
