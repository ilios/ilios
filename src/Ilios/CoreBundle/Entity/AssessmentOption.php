<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Traits\SessionTypesEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;


use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class AssessmentOption
 *
 * @ORM\Table(name="assessment_option",uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AssessmentOptionRepository")
 *
 * @IS\Entity
 */
class AssessmentOption implements AssessmentOptionInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;

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
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
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
    *
    * @IS\Expose
    * @IS\Type("string")
    */
    protected $name;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     *
     * @ORM\OneToMany(targetEntity="SessionType", mappedBy="assessmentOption")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
    }
}
