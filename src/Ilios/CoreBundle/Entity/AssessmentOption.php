<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;


use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class AssessmentOption
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="assessment_option",uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 *
 * @JMS\ExclusionPolicy("all")
 */
class AssessmentOption implements AssessmentOptionInterface
{
//    use IdentifiableEntity; //Implement in 3.1
    use NameableEntity;
    use StringableIdEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, name="assessment_option_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=20)
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 18
    * )    
    */
    protected $name;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     *
     * @ORM\OneToMany(targetEntity="SessionType", mappedBy="assessmentOption")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionTypes")
     */
    protected $sessionTypes;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->assessmentOptionId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->assessmentOptionId : $this->id;
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
