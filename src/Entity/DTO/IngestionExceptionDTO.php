<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class IngestionExceptionDTO
 * Data transfer object for an ingestionException
 * @IS\DTO("ingestionExceptions")
 */
class IngestionExceptionDTO
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
    public string $uid;

    /**
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("integer")
     */
    public int $user;

    public function __construct(int $id, string $uid)
    {
        $this->id = $id;
        $this->uid = $uid;
    }
}
