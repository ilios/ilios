<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;

/**
 * Interface AuthenticationInterface
 */
interface AuthenticationInterface extends LoggableEntityInterface
{
    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $passwordSha256
     */
    public function setPasswordSha256($passwordSha256);

    /**
     * @return string
     */
    public function getPasswordSha256();

    /**
     * @param string $passwordBcrypt
     */
    public function setPasswordBcrypt($passwordBcrypt);

    /**
     * @return string
     */
    public function getPasswordBcrypt();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * Check if this is a legacy account
     * @return boolean
     */
    public function isLegacyAccount();


    /**
     * @param \DateTime $invalidateTokenIssuedBefore
     */
    public function setInvalidateTokenIssuedBefore(\DateTime $invalidateTokenIssuedBefore = null);


    /**
     * @return \DateTime
     */
    public function getInvalidateTokenIssuedBefore();

    /**
     * @return SessionUserInterface
     */
    public function getSessionUser();
}
