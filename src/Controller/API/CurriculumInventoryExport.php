<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventoryExportManager;

class CurriculumInventoryExport extends ReadWriteController
{
    public function __construct(CurriculumInventoryExportManager $manager)
    {
        parent::__construct($manager, 'curriculuminventoryexports');
    }
}
