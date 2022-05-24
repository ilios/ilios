<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;

#[IA\DTO("aamcMethods")]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "AamcMethod",
    properties: [
        new OA\Property(
            "id",
            type:"string"
        ),
        new OA\Property(
            "description",
            type:"string"
        ),
        new OA\Property(
            "active",
            type:"boolean"
        ),
        new OA\Property(
            "sessionTypes",
            type:"array",
            items: new OA\Items("string"),
        )
    ]
)]
class AamcMethodDTO
{
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type("array<string>")]
    public array $sessionTypes = [];

    public function __construct(
        #[IA\Expose]
        #[IA\Id]
        #[IA\Type("string")]
        public string $id,
        #[IA\Expose]
        #[IA\Type("string")]
        public string $description,
        #[IA\Expose]
        #[IA\Type("boolean")]
        public bool $active
    ) {
    }
}
