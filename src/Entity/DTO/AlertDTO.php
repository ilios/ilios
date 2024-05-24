<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('alerts')]
#[OA\Schema(
    title: "Alert",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "tableRowId",
            description: "Table row ID",
            type: "string"
        ),
        new OA\Property(
            "tableName",
            description: "Table name",
            type: "string"
        ),
        new OA\Property(
            "additionalText",
            description: "Additional text",
            type: "string"
        ),
        new OA\Property(
            "dispatched",
            description: "Has been dispatched",
            type: "string",
        ),
        new OA\Property(
            "alertChangeTypes",
            description: "Alert change types",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instigators",
            description: "Users that instigated the alert",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "recipients",
            description: "Recipient schools",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class AlertDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $tableRowId;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $tableName;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $additionalText;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $dispatched;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('alertChangeTypes')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $changeTypes = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instigators = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $recipients = [];

    public function __construct(
        int $id,
        int $tableRowId,
        string $tableName,
        ?string $additionalText,
        bool $dispatched
    ) {
        $this->id = $id;
        $this->tableRowId = $tableRowId;
        $this->tableName = $tableName;
        $this->additionalText = $additionalText;
        $this->dispatched = $dispatched;
    }
}
