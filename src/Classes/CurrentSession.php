<?php

declare(strict_types=1);

namespace App\Classes;

use App\Classes\SessionUserInterface;
use App\Entity\UserInterface;
use App\Attribute as IA;

/**
 * Class CurrentSession
 */
#[IA\DTO]
class CurrentSession
{
    /**
     * @var int
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public $userId;
    /**
     * Constructor
     */
    public function __construct(SessionUserInterface $user)
    {
        $this->userId = $user->getId();
    }
}
