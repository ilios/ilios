<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterial;
use Ilios\CoreBundle\Traits\DescribableTrait;

class Citation extends LearningMaterial implements CitationInterface
{
    /**
     * renamed from citation
     */
    use DescribableTrait;
}
