<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface LearningMaterialUserRoleInterface
 */
interface LearningMaterialUserRoleInterface 
{
    public function getLearningMaterialUserRoleId();

    public function setTitle($title);

    public function getTitle();

    public function addLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial);

    public function removeLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial);

    public function getLearningMaterials();
}
