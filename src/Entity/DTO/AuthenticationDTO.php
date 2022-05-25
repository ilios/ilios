<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('authentications')]
#[OA\Schema(
    title: "Authentication",
    properties: [
        new OA\Property(
            "user",
            description: "User ID",
            type: "integer"
        ),
        new OA\Property(
            "username",
            description: "Username",
            type: "string"
        )
    ]
)]
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
