<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Interface LearningMaterialsEntityInterface
 */
interface LearningMaterialsEntityInterface
{
    /**
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials);

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
    * @return LearningMaterialInterface[]|ArrayCollection
    */
    public function getLearningMaterials();
}
