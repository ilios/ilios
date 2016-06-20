<?php
namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

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
     * @JMS\Type("string")
    */
    public $user;

    /**
     * @var string
     * @JMS\Type("string")
     *
    */
    public $username;

    /**
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("invalidateTokenIssuedBefore")
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
