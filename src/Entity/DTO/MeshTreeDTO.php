<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class MeshTreeDTO
 *
 * @IS\DTO("meshTrees")
 */
class MeshTreeDTO
{
    /**
     * @var string
     * @IS\Id
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
