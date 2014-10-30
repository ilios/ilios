<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialUserRoleInterface
 * @package Ilios\CoreBundle\Model
 */
interface LearningMaterialUserRoleInterface extends IdentifiableEntityInterface, TitledEntityInterface
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

