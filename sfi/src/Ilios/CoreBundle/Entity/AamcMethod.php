<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Entity\SessionTypeInterface;
use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;
use Ilios\CoreBundle\Traits\StringableUuidEntity;

/**
 * Class AamcMethod
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="aamc_method")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class AamcMethod implements AamcMethodInterface
{
//    use UniversallyUniqueEntity;
    use DescribableEntity;
    use StringableUuidEntity;

    /**
     * @deprecated replace with UniversallyUniqueEntity trait for 3.1.x
     * @var string
     *
     * @ORM\Column(name="method_id", type="string", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     *
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("id")
     */
    protected $uuid;

    /**
    * @ORM\Column(name="description", type="text")
    * @var string
    */
    protected $description;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     *
     * @ORM\ManyToMany(targetEntity="SessionType", mappedBy="aamcMethods")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionTypes")
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
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->methodId = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->methodId : $this->uuid;
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
