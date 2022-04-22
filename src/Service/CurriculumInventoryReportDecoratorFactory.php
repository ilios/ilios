<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class CurriculumInventoryReportDecoratorFactory
{
    public function __construct(protected RouterInterface $router)
    {
    }

    public function create(CurriculumInventoryReportDTO $report): CurriculumInventoryReportDTO
    {
        $report->absoluteFileUri = $this->router->generate(
            'app_curriculuminventorydownload_get',
            ['token' => $report->token],
            UrlGenerator::ABSOLUTE_URL
        );

        return $report;
    }
}
