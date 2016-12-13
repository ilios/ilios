<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\SessionTypesEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\SessionTypeInterface;
use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class AamcMethod
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="aamc_method")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AamcMethodRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class AamcMethod implements AamcMethodInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="method_id", type="string", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $id;

    /**
    * @ORM\Column(name="description", type="text")
    * @var string
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 65000
    * )
    *
    * @JMS\Expose
    * @JMS\Type("string")
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
     * @inheritdoc
     */
    public function addSessionType(SessionTypeInterface $sessionType)
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
            $sessionType->addAamcMethod($this);
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function removeSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->removeElement($sessionType);
        $sessionType->removeAamcMethod($this);
    }
}
