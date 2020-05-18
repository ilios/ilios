<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventorySequenceManager;

class CurriculumInventorySequence extends ReadWriteController
{
    public function __construct(CurriculumInventorySequenceManager $manager)
    {
        parent::__construct($manager, 'curriculuminventorysequences');
    }
}
