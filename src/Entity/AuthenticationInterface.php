<?php

declare(strict_types=1);

namespace App\Entity;

use App\Classes\SessionUserInterface;

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
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash);

    /**
     * @return string
     */
    public function getPasswordHash();

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
     * @param \DateTime $invalidateTokenIssuedBefore
     */
    public function setInvalidateTokenIssuedBefore(\DateTime $invalidateTokenIssuedBefore = null);


    /**
     * @return \DateTime
     */
    public function getInvalidateTokenIssuedBefore();
}
