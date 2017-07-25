<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class IngestionExceptionDTO
 * Data transfer object for an ingestionException
 *
 * @IS\DTO
 */
class IngestionExceptionDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;
    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $uid;

    /**
     * @var integer
     * @IS\Expose
     *
     * @IS\Type("string")
     */
    public $user;

    public function __construct($id, $uid)
    {
        $this->id = $id;
        $this->uid = $uid;
    }
}
