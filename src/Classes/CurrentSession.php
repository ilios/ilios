<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('currentSession')]
#[OA\Schema(
    title: "CurrentSession",
    properties: [
        new OA\Property(
            "userId",
            description: "The user ID",
            type: "string"
        )
    ]
)]
class CurrentSession
{
    #[IA\Expose]
    #[IA\Type('string')]
    public int $userId;

    public function __construct(SessionUserInterface $user)
    {
        $this->userId = $user->getId();
    }
}
