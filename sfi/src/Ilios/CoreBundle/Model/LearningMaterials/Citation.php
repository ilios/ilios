<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterial;

class Citation extends LearningMaterial implements CitationInterface
{
    /**
     * Used only by citation
     * @var string
     */
    private $citation;
}
