<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

/**
 * Interface LinkInterface
 * @package Ilios\CoreBundle\Model\LearningMaterials
 */
interface LinkInterface
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
