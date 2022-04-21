<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\LearnerGroupRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/learnergroups')]
class LearnerGroups extends ReadWriteController
{
    public function __construct(LearnerGroupRepository $repository)
    {
        parent::__construct($repository, 'learnergroups');
    }
}
