<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AamcMethodDTO
 * Data transfer object for a aamcMethod
 *
 * @IS\DTO("aamcMethods")
 */
class AamcMethodDTO
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
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionTypes;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $active;


    public function __construct(
        string $id,
        string $description,
        bool $active
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->active = $active;

        $this->sessionTypes = [];
    }
}
