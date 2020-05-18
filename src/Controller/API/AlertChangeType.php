<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AlertChangeTypeManager;

class AlertChangeType extends ReadWriteController
{
    public function __construct(AlertChangeTypeManager $manager)
    {
        parent::__construct($manager, 'alertchangetypes');
    }
}
