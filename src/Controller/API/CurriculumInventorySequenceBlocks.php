<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventorySequenceBlockManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/curriculuminventorysequenceblocks")
 */
class CurriculumInventorySequenceBlocks extends ReadWriteController
{
    public function __construct(CurriculumInventorySequenceBlockManager $manager)
    {
        parent::__construct($manager, 'curriculuminventorysequenceblocks');
    }
}
