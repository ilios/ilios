<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class MeshConceptDTO
 *
 * @IS\DTO("meshConcepts")
 */
class MeshConceptDTO
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
     * @IS\Type("boolean")
     */
    public bool $preferred;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $scopeNote;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $casn1Name;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $registryNumber;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("meshTerms")
     * @IS\Type("array<string>")
     */
    public array $terms = [];

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
     * @var int[]
     * @IS\Expose
     * @IS\Related("meshDescriptors")
     * @IS\Type("array<string>")
     */
    public array $descriptors = [];

    public function __construct(
        string $id,
        string $name,
        bool $preferred,
        string $scopeNote,
        string $casn1Name,
        string $registryNumber,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->preferred = $preferred;
        $this->scopeNote = $scopeNote;
        $this->casn1Name = $casn1Name;
        $this->registryNumber = $registryNumber;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
