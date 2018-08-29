<?php
namespace Ilios\CoreBundle\Service;

use AppBundle\Entity\DTO\CurriculumInventoryReportDTO;
use AppBundle\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class CurriculumInventoryReportDecoratorFactory
{

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $entityDecoratorClassName;

    /**
     * @param RouterInterface $router
     * @param string $decoratorClassName
     */
    public function __construct(RouterInterface $router, $decoratorClassName)
    {
        $this->router = $router;
        $this->entityDecoratorClassName = $decoratorClassName;
    }

    /**
     * @param $report
     * @return CurriculumInventoryReportDTO
     * @throws \Exception
     */
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
