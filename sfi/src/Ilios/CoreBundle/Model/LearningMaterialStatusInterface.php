<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\TitleTraitInterface;

/**
 * Interface LearningMaterialStatusInterface
 * @package Ilios\CoreBundle\Model
 */
interface LearningMaterialStatusInterface extends IdentifiableTraitInterface, TitleTraitInterface
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
     * @return ArrayCollection|LearningMaterialInterface[]
     */
    public function getLearningMaterials();
}
