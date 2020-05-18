<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ReportManager;

class Report extends ReadWriteController
{
    public function __construct(ReportManager $manager)
    {
        parent::__construct($manager, 'reports');
    }
}
