<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Model\SessionTypeInterface;
use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\NameableTrait;

/**
 * Class AamcMethod
 * @package Ilios\CoreBundle\Model
 */
class AamcMethod implements AamcMethodInterface
{
    use IdentifiableTrait;
    use NameableTrait;
    use DescribableTrait;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     */
    protected $sessionTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
    }

    /**
     * @param Collection $sessionTypes
     */
    public function setSessionTypes(Collection $sessionTypes)
    {
        $this->sessionTypes = new ArrayCollection();

        foreach ($sessionTypes as $sessionType) {
            $this->addSessionType($sessionType);
        }
    }

    /**
     * Add sessionTypes
     *
     * @param SessionTypeInterface $sessionType
     */
    public function addSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->add($sessionType);
    }

    /**
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function getSessionTypes()
    {
        return $this->sessionTypes;
    }
}
