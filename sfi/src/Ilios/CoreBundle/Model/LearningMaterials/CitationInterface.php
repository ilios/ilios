<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterialInterface;

interface CitationInterface extends LearningMaterialInterface
{
    /**
     * @param string $text
     */
    public function setText($text);

    /**
     * @return string
     */
    public function getText();
}
