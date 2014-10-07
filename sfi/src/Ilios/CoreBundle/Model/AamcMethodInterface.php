<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Model\SessionTypeInterface;

use Ilios\CoreBundle\Traits\DescribableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\NameableTraitInterface;

/**
 * Interface AamcMethodInterface
 * @package Ilios\CoreBundle\Model
 */
interface AamcMethodInterface extends
    IdentifiableTraitIntertface,
    NameableTraitInterface,
    DescribableTraitInterface
{
    /**
     * @param Collection $sessionTypes
     */
    public function setSessionTypes(Collection $sessionTypes);

    /**
     * Add sessionTypes
     *
     * @param SessionTypeInterface $sessionType
     */
    public function addSessionType(SessionTypeInterface $sessionType);

    /**
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function getSessionTypes();
}

