<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ReportManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/reports")
 */
class Reports extends ReadWriteController
{
    public function __construct(ReportManager $manager)
    {
        parent::__construct($manager, 'reports');
    }
}
