<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class MeshPreviousIndexingDTO
 * Data transfer object for a MeSH descriptor.
 *
 * @IS\DTO
 */
class MeshPreviousIndexingDTO
{
    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $previousIndexing;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $descriptor;

    public function __construct($id, $previousIndexing)
    {
        $this->id = $id;
        $this->previousIndexing = $previousIndexing;
    }
}
