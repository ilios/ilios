<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class PendingUserUpdateDTO
 *
 * @IS\DTO("pendingUserUpdates")
 */
class PendingUserUpdateDTO
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
    public string $type;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $property;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $value;

    /**
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("string")
     */
    public int $user;

    public function __construct(int $id, string $type, string $property, string $value)
    {
        $this->id = $id;
        $this->type = $type;
        $this->property = $property;
        $this->value = $value;
    }
}
