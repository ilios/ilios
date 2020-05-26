<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class IngestionExceptionDTO
 * Data transfer object for an ingestionException
 *
 * @IS\DTO("ingestionExceptions")
 */
class IngestionExceptionDTO
{
    /**
     * @var int
     * @IS\Id
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
     * @var int
     * @IS\Expose
     *
     * @IS\Related("users")
     * @IS\Type("string")
     */
    public $user;

    public function __construct($id, $uid)
    {
        $this->id = $id;
        $this->uid = $uid;
    }
}
