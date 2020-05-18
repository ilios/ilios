<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventorySequenceBlockManager;

class CurriculumInventorySequenceBlock extends ReadWriteController
{
    public function __construct(CurriculumInventorySequenceBlockManager $manager)
    {
        parent::__construct($manager, 'curriculuminventorysequenceblocks');
    }
}
