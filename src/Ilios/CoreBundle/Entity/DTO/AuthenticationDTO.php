<?php
namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class Authentication
 *
 * @IS\DTO
 */
class AuthenticationDTO
{
    /**
     * @var int
     * @IS\Expose
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

    /**
     * @IS\Type("dateTime")
     * @IS\Expose
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
