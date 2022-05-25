<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('ingestionExceptions')]
#[OA\Schema(
    title: "IlmSession",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "uid",
            description: "UID",
            type: "string"
        ),
        new OA\Property(
            "user",
            description: "User",
            type: "integer"
        )
    ]
)]
class IngestionExceptionDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $uid;

    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('integer')]
    public int $user;

    public function __construct(int $id, string $uid)
    {
        $this->id = $id;
        $this->uid = $uid;
    }
}
