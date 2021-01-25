<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class MeshPreviousIndexingDTO
 * Data transfer object for a MeSH descriptor.
 *
 * @IS\DTO("meshPreviousIndexings")
 */
class MeshPreviousIndexingDTO
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
    public string $previousIndexing;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $descriptor;

    public function __construct(int $id, string $previousIndexing)
    {
        $this->id = $id;
        $this->previousIndexing = $previousIndexing;
    }
}
