<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class AlertDTO
 * Data transfer object for an alert
 */
#[IA\DTO('alerts')]
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
    #[IA\Type('array<string>')]
    public array $changeTypes = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $instigators = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('array<string>')]
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
