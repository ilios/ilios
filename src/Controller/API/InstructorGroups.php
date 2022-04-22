<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\InstructorGroupRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/instructorgroups')]
class InstructorGroups extends ReadWriteController
{
    public function __construct(InstructorGroupRepository $repository)
    {
        parent::__construct($repository, 'instructorgroups');
    }
}
