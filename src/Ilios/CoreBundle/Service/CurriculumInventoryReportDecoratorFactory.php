<?php
namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryReportDTO;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

class CurriculumInventoryReportDecoratorFactory
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $entityDecoratorClassName;

    /**
     * @param Router $router
     * @param string $decoratorClassName
     */
    public function __construct(Router $router, $decoratorClassName)
    {
        $this->router = $router;
        $this->entityDecoratorClassName = $decoratorClassName;
    }

    public function create(
        $report
    ) {
        if ($report instanceof CurriculumInventoryReportInterface) {
            return new $this->entityDecoratorClassName($report, $this->router);
        }

        if ($report instanceof CurriculumInventoryReportDTO) {
            return $this->decorateDto($report);
        }

        throw new \Exception(get_class($report) . " cannot be decorated");
    }

    /**
     * @param CurriculumInventoryReportDTO $reportDTO
     *
     * @return CurriculumInventoryReportDTO
     */
    protected function decorateDto(CurriculumInventoryReportDTO $reportDTO)
    {
        $reportDTO->absoluteFileUri = $this->router->generate(
            'ilios_core_downloadcurriculuminventoryreport',
            ['token' => $reportDTO->token],
            UrlGenerator::ABSOLUTE_URL
        );

        return $reportDTO;
    }
}
