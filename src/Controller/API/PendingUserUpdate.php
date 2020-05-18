<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\PendingUserUpdateManager;

class PendingUserUpdate extends ReadWriteController
{
    public function __construct(PendingUserUpdateManager $manager)
    {
        parent::__construct($manager, 'pendinguserupdates');
    }
}
