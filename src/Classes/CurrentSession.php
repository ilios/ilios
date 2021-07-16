<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attribute as IA;

/**
 * Class CurrentSession
 */
#[IA\DTO('currentSession')]
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
