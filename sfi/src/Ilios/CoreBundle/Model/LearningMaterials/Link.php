<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterial;

/**
 * Class Link
 * @package Ilios\CoreBundle\Model\LearningMaterials
 */
class Link extends LearningMaterial implements LinkInterface
{
    /**
     * @var string
     */
    protected $webLink;

    /**
     * @param string $webLink
     */
    public function setWebLink($webLink)
    {
        $this->webLink = $webLink;
    }

    /**
     * @return string
     */
    public function getWebLink()
    {
        return $this->webLink;
    }
}
