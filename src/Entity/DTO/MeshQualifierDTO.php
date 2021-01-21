<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class MeshQualifierDTO
 *
 * @IS\DTO("meshQualifiers")
 */
class MeshQualifierDTO
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
     */
    public string $name;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $createdAt;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $updatedAt;

    /**
     * @var string[]
     * @IS\Expose
     * @IS\Related("meshDescriptors")
     * @IS\Type("array<string>")
     */
    public array $descriptors;

    public function __construct(string $id, string $name, DateTime $createdAt, DateTime $updatedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->descriptors = [];
    }
}
