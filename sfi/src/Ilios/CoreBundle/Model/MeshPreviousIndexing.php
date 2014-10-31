<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;

/**
 * Class MeshPreviousIndexing
 * @package Ilios\CoreBundle\Model
 */
class MeshPreviousIndexing implements MeshPreviousIndexingInterface
{
    use UniversallyUniqueEntity;

    /**
     * @var string
     */
    private $previousIndexing;

    /**
     * @param string $previousIndexing
     */
    public function setPreviousIndexing($previousIndexing)
    {
        $this->previousIndexing = $previousIndexing;
    }

    /**
     * @return string
     */
    public function getPreviousIndexing()
    {
        return $this->previousIndexing;
    }
}
