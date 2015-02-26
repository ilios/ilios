<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class LearningMaterialUserRole
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="learning_material_user_role")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class LearningMaterialUserRole implements LearningMaterialUserRoleInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="learning_material_user_role_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $title;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearningMaterial", mappedBy="userRole")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learningMaterials")
     */
    protected $learningMaterials;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learningMaterials = new ArrayCollection();
    }

    /**
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials)
    {
        $this->learningMaterials = new ArrayCollection();

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->add($learningMaterial);
    }

    /**
     * @return ArrayCollection|LearningMaterialInterface[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials;
    }
}
