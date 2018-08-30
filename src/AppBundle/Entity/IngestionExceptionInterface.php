<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface IngestionExceptionInterface
 */
interface IngestionExceptionInterface extends IdentifiableEntityInterface
{
    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param string $uid
     */
    public function setUid($uid);

    /**
     * @return string
     */
    public function getUid();
}
