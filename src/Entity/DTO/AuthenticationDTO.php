<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

#[IA\DTO('authentications')]
class AuthenticationDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('integer')]
    public int $user;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $username;

    public function __construct(
        int $user,
        ?string $username
    ) {
        $this->user = $user;
        $this->username = $username;
    }
}
