<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AlertManager;

class Alert extends ReadWriteController
{
    public function __construct(AlertManager $manager)
    {
        parent::__construct($manager, 'alerts');
    }
}
