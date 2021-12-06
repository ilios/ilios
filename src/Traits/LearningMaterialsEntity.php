<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\LearningMaterialInterface;

/**
 * Class LearningMaterialsEntity
 */
trait LearningMaterialsEntity
{
    public function setLearningMaterials(Collection $learningMaterials)
    {
        $this->learningMaterials = new ArrayCollection();

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    public function addLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    /**
    * @return LearningMaterialInterface[]|ArrayCollection
    */
    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }
}
