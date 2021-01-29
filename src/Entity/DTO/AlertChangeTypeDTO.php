<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AlertChangeTypeDTO
 * Data transfer object for a alertChangeType
 *
 * @IS\DTO("alertChangeTypes")
 */
class AlertChangeTypeDTO
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
     *
     */
    public string $title;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $alerts = [];

    public function __construct(
        int $id,
        string $title
    ) {
        $this->id = $id;
        $this->title = $title;
    }
}
