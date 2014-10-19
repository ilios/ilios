<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\TitleTraitInterface;

/**
 * Interface LearningMaterialUserRoleInterface
 * @package Ilios\CoreBundle\Model
 */
interface LearningMaterialUserRoleInterface extends IdentifiableTraitIntertface, TitleTraitInterface
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

