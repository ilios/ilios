<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AamcPcrsDTO
 * Data transfer object for a aamcPcrs
 *
 * @IS\DTO("aamcPcrses")
 */
class AamcPcrsDTO
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
    public string $description;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $competencies = [];

    public function __construct(
        string $id,
        string $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
