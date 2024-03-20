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
    protected Collection $learningMaterials;

    public function setLearningMaterials(Collection $learningMaterials): void
    {
        $this->learningMaterials = new ArrayCollection();

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    public function addLearningMaterial(LearningMaterialInterface $learningMaterial): void
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial): void
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }
}
