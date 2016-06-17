<?php
namespace Ilios\CoreBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

class CurriculumInventoryReportDecoratorFactory
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $decoratorClassName;

    /**
     * @param Router $router
     * @param string $decoratorClassName
     */
    public function __construct($router, $decoratorClassName)
    {
        $this->router = $router;
        $this->decoratorClassName = $decoratorClassName;
    }

    public function create(
        CurriculumInventoryReportInterface $report
    ) {
        $decorator = new $this->decoratorClassName($report, $this->router);

        return $decorator;
    }
}
