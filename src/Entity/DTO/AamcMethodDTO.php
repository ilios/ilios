<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO("aamcMethods")]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "AamcMethod",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "string"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "active",
            description:"Active",
            type:"boolean"
        ),
        new OA\Property(
            "sessionTypes",
            description: "Session types",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class AamcMethodDTO
{
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
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
