<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

#[IA\DTO('pendingUserUpdates')]
class PendingUserUpdateDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $type;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $property;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $value;

    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('integer')]
    public int $user;

    public function __construct(int $id, string $type, ?string $property, ?string $value)
    {
        $this->id = $id;
        $this->type = $type;
        $this->property = $property;
        $this->value = $value;
    }
}
