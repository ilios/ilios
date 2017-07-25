<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class LearningMaterialStatusDTO
 * Data transfer object for a learning material status
 *
 * @IS\DTO
 */
class LearningMaterialStatusDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * Constructor
     */
    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
