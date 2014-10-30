<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class LearningMaterialStatus
 * @package Ilios\CoreBundle\Model
 */
class LearningMaterialStatus implements LearningMaterialStatusInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
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
