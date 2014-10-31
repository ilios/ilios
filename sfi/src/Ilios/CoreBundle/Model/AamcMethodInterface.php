<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Model\SessionTypeInterface;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface AamcMethodInterface
 * @package Ilios\CoreBundle\Model
 */
interface AamcMethodInterface extends
    UniversallyUniqueEntityInterface,
    DescribableEntityInterface
{
    /**
     * @param Collection $sessionTypes
     */
    public function setSessionTypes(Collection $sessionTypes);

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function addSessionType(SessionTypeInterface $sessionType);

    /**
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function getSessionTypes();
}

