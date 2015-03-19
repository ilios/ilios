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
     * @param string $link
     */
    public function setLink($link);

    /**
     * @return string
     */
    public function getLink();
}
