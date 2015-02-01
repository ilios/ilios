<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class LearningMaterialStatus
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="learning_material_status")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class LearningMaterialStatus implements LearningMaterialStatusInterface
{
//    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Column(name="learning_material_status_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @ORM\Column(type="string", length=60)
    * @todo should be on the TitledEntity Trait
    * @var string
    */
    protected $title;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearningMaterial", mappedBy="status")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->learningMaterialStatusId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->learningMaterialStatusId : $this->id;
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
