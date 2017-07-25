<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class MeshTreeDTO
 *
 * @IS\DTO
 */
class MeshTreeDTO
{
    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $treeNumber;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $descriptor;

    /**
     * @param $id
     * @param $treeNumber
     */
    public function __construct($id, $treeNumber)
    {
        $this->id = $id;
        $this->treeNumber = $treeNumber;
    }
}
