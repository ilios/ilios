<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

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
