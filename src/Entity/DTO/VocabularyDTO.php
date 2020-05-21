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
     * @var string
     * @IS\Id
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var int[]
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var bool
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $active;

    /**
     * @param $id
     * @param $title
     * @param $active
     */
    public function __construct($id, $title, $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;

        $this->terms = [];
    }
}
