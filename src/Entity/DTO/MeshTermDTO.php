<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class MeshTermDTO
 *
 * @IS\DTO("meshTerms")
 */
class MeshTermDTO
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
    public string $meshTermUid;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $name;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $lexicalTag;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $conceptPreferred;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $recordPreferred;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $permuted;

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
     *
     * @IS\Expose
     * @IS\Related("meshConcepts")
     * @IS\Type("array<string>")
     */
    public array $concepts = [];

    public function __construct(
        int $id,
        string $meshTermUid,
        string $name,
        string $lexicalTag,
        bool $conceptPreferred,
        bool $recordPreferred,
        bool $permuted,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->meshTermUid = $meshTermUid;
        $this->name = $name;
        $this->lexicalTag = $lexicalTag;
        $this->conceptPreferred = $conceptPreferred;
        $this->recordPreferred = $recordPreferred;
        $this->permuted = $permuted;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
