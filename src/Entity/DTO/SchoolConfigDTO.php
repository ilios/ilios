<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SchoolConfigDTO
 *
 * @IS\DTO("schoolConfigs")
 */
class SchoolConfigDTO
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
    public string $name;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $value;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("integer")
     */
    public int $school;

    public function __construct(int $id, string $name, string $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }
}
