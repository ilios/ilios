<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\LearningMaterialInterface;

/**
 * Interface LearningMaterialsEntityInterface
 */
interface LearningMaterialsEntityInterface
{
    public function setLearningMaterials(Collection $learningMaterials);

    public function addLearningMaterial(LearningMaterialInterface $learningMaterial);

    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
    * @return LearningMaterialInterface[]|ArrayCollection
    */
    public function getLearningMaterials();
}
