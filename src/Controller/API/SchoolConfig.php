<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SchoolConfigManager;

class SchoolConfig extends ReadWriteController
{
    public function __construct(SchoolConfigManager $manager)
    {
        parent::__construct($manager, 'schoolconfigs');
    }
}
