<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\InstructorGroupManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/instructorgroups")
 */
class InstructorGroups extends ReadWriteController
{
    public function __construct(InstructorGroupManager $manager)
    {
        parent::__construct($manager, 'instructorgroups');
    }
}
