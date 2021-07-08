<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class IngestionExceptionDTO
 * Data transfer object for an ingestionException
 */
#[IA\DTO('ingestionExceptions')]
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
