<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class LearningMaterialsEntity
 */
trait LearningMaterialsEntity
{
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
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    /**
    * @return LearningMaterialInterface[]|ArrayCollection
    */
    public function getLearningMaterials()
    {
        return $this->learningMaterials;
    }
}
