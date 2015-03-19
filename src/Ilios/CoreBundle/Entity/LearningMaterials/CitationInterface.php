<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

interface CitationInterface extends LearningMaterialInterface
{
    /**
     * @param string $text
     */
    public function setCitation($citation);

    /**
     * @return string
     */
    public function getCitation();
}
