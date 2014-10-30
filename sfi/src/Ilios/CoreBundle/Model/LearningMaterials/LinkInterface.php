<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterialInterface;

/**
 * Interface LinkInterface
 * @package Ilios\CoreBundle\Model\LearningMaterials
 */
interface LinkInterface extends LearningMaterialInterface
{
    /**
     * @param string $webLink
     */
    public function setWebLink($webLink);

    /**
     * @return string
     */
    public function getWebLink();
}
