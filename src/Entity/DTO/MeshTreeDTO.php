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
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $treeNumber;

    /**
     * @IS\Expose
     * @IS\Related("meshDescriptors")
     * @IS\Type("string")
     */
    public string $descriptor;

    public function __construct(int $id, string $treeNumber)
    {
        $this->id = $id;
        $this->treeNumber = $treeNumber;
    }
}
