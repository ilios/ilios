<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('alertChangeTypes')]
#[OA\Schema(
    title: "AlertChangeType",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "title",
            description: "Title",
            type: "string"
        ),
        new OA\Property(
            "alerts",
            description: "Alerts",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class AlertChangeTypeDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $alerts = [];

    public function __construct(
        int $id,
        string $title
    ) {
        $this->id = $id;
        $this->title = $title;
    }
}
