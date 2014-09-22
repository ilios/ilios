<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface LearningMaterialStatusInterface
 */
interface LearningMaterialStatusInterface 
{
    public function getLearningMaterialStatusId();

    public function setTitle($title);

    public function getTitle();

    public function addLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial);

    public function removeLearningMaterial(\Ilios\CoreBundle\Model\LearningMaterial $learningMaterial);

    public function getLearningMaterials();
}
