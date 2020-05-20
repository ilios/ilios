<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearnerGroupManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/learnergroups")
 */
class LearnerGroups extends ReadWriteController
{
    public function __construct(LearnerGroupManager $manager)
    {
        parent::__construct($manager, 'learnergroups');
    }
}
