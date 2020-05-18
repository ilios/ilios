<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionManager;

class Session extends ReadWriteController
{
    public function __construct(SessionManager $manager)
    {
        parent::__construct($manager, 'sessions');
    }
}
