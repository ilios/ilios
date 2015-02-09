<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface IngestionExceptionInterface
 * @package Ilios\CoreBundle\Entity
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
}
