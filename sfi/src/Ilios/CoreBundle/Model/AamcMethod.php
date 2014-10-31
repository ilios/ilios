<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Model\SessionTypeInterface;
use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;

/**
 * Class AamcMethod
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="aamc_method")
 */
class AamcMethod implements AamcMethodInterface
{
    use UniversallyUniqueEntity;
    use DescribableEntity;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     *
     * @ORM\ManyToMany(targetEntity="SessionType", mappedBy="aamcMethods")
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
