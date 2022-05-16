<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

#[IA\DTO('userRoles')]
class UserRoleDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
