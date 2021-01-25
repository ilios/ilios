<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class VocabularyDTO
 *
 * @IS\DTO("vocabularies")
 */
class VocabularyDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("integer")
     */
    public int $school;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $terms = [];

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $active;

    public function __construct(int $id, string $title, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
    }
}
