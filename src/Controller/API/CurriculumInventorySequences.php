<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventorySequenceManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/curriculuminventorysequences")
 */
class CurriculumInventorySequences extends ReadWriteController
{
    public function __construct(CurriculumInventorySequenceManager $manager)
    {
        parent::__construct($manager, 'curriculuminventorysequences');
    }
}
