<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class VocabularyDTO
 *
 * @IS\DTO
 */
class VocabularyDTO
{
    /**
     * @var string
     *
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
     * @var integer[]
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var boolean
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
