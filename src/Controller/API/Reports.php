<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\ReportRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/reports")
 */
class Reports extends ReadWriteController
{
    public function __construct(ReportRepository $repository)
    {
        parent::__construct($repository, 'reports');
    }
}
