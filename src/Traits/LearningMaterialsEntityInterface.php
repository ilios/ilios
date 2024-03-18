<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\LearningMaterialInterface;

/**
 * Interface LearningMaterialsEntityInterface
 */
interface LearningMaterialsEntityInterface
{
    public function setLearningMaterials(Collection $learningMaterials): void;

    public function addLearningMaterial(LearningMaterialInterface $learningMaterial): void;

    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial): void;

    public function getLearningMaterials(): Collection;
}
