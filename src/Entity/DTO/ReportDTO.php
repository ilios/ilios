<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class ReportDTO
 */
#[IA\DTO('reports')]
#[IA\ExposeGraphQL]
class ReportDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $title;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public ?int $school = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $subject;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $prepositionalObject;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $prepositionalObjectTableRowId;

    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('integer')]
    public int $user;

    public function __construct(
        int $id,
        ?string $title,
        DateTime $createdAt,
        string $subject,
        ?string $prepositionalObject,
        ?string $prepositionalObjectTableRowId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->subject = $subject;
        $this->prepositionalObject = $prepositionalObject;
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }
}
