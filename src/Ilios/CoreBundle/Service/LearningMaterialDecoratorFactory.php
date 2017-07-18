<?php
namespace Ilios\CoreBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

class LearningMaterialDecoratorFactory
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
    public function __construct(Router $router, $decoratorClassName)
    {
        $this->router = $router;
        $this->decoratorClassName = $decoratorClassName;
    }
    
    public function create(
        LearningMaterialInterface $learningMaterialEntity
    ) {
        $decorator = new $this->decoratorClassName($learningMaterialEntity, $this->router);

        return $decorator;
    }
}
