<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface IngestionExceptionInterface
 * @package Ilios\CoreBundle\Entity
 */
interface IngestionExceptionInterface extends UniversallyUniqueEntityInterface
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
