<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventoryReportManager;

class CurriculumInventoryReport extends ReadWriteController
{
    public function __construct(CurriculumInventoryReportManager $manager)
    {
        parent::__construct($manager, 'curriculuminventoryreports');
    }
}
