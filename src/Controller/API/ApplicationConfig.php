<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ApplicationConfigManager;

class ApplicationConfig extends ReadWriteController
{
    public function __construct(ApplicationConfigManager $manager)
    {
        parent::__construct($manager, 'applicationconfigs');
    }
}
