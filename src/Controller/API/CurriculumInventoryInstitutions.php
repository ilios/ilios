<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventoryInstitutionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/curriculuminventoryinstitutions")
 */
class CurriculumInventoryInstitutions extends ReadWriteController
{
    public function __construct(CurriculumInventoryInstitutionManager $manager)
    {
        parent::__construct($manager, 'curriculuminventoryinstitutions');
    }
}
