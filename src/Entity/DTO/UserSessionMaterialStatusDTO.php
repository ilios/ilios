<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO("userSessionMaterialStatuses")]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "UserSessionMaterialStatus",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "user",
            description: "User",
            type: "integer"
        ),
        new OA\Property(
            "material",
            description: "SessionLearningMaterial",
            type: "integer"
        ),
        new OA\Property(
            "status",
            description: "Status",
            type: "integer"
        ),
        new OA\Property(
            "updatedAt",
            description: "Last Updated At",
            type: "string",
            format: "date-time"
        ),
    ]
)]
#[IA\FilterableBy('statuses', IA\Type::INTEGERS)]
#[IA\FilterableBy('materials', IA\Type::INTEGERS)]
#[IA\FilterableBy('users', IA\Type::INTEGERS)]
class UserSessionMaterialStatusDTO
{
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('integer')]
    public int $user;

    #[IA\Expose]
    #[IA\Related('sessionLearningMaterials')]
    #[IA\Type('integer')]
    public int $material;

    public function __construct(
        #[IA\Expose]
        #[IA\Id]
        #[IA\Type("integer")]
        public int $id,
        #[IA\Expose]
        #[IA\Type("integer")]
        public int $status,
        #[IA\Expose]
        #[IA\Type("dateTime")]
        public Datetime $updatedAt,
    ) {
    }
}
