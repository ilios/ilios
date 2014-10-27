<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterial;

class Citation extends LearningMaterial implements CitationInterface
{
    /**
     * renamed from citation
     * @var string
     */
    protected $text;

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->setType(self::TYPE_CITATION);
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
