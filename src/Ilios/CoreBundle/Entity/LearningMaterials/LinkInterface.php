<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Interface LinkInterface
 * @package Ilios\CoreBundle\Entity\LearningMaterials
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
