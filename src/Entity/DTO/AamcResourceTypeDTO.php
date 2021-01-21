<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AamcResourceTypeDTO
 * Data transfer object for a aamcResourceType
 *
 * @IS\DTO("aamcResourceTypes")
 */
class AamcResourceTypeDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public string $description;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $terms;

    public function __construct(
        string $id,
        string $title,
        string $description
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;

        $this->terms = [];
    }
}
