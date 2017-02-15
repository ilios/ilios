<?php

namespace Ilios\CliBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Inflector\Inflector;
use Ilios\CoreBundle\Service\EntityMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Generates the YAML file for the swagger definitino docs
 *
 * Class GenerateSwaggerApiPathYamlCommand
 * @package Ilios\CliBUndle\Command
 */
class GenerateSwaggerApiDefinitionYamlCommand extends Command
{
    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var EntityMetadata
     */
    protected $entityMetadata;

    /**
     *
     * @param EngineInterface $templatingEngine
     * @param Registry $registry
     * @param EntityMetadata $entityMetadata
     */
    public function __construct(
        EngineInterface $templatingEngine,
        Registry $registry,
        EntityMetadata $entityMetadata
    ) {
        parent::__construct();
        $this->templatingEngine = $templatingEngine;
        $this->registry   = $registry;
        $this->entityMetadata   = $entityMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:generate:swagger-definition')
            ->setDescription('Creates standard swagger definition yaml file for an entity.')
            ->addArgument(
                'entityShortcut',
                InputArgument::REQUIRED,
                'The name of an entity e.g. IliosCoreBundle:Session.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shortCut = $input->getArgument('entityShortcut');

        $manager = $this->registry->getManagerForClass($shortCut);
        $class = $manager->getClassMetadata($shortCut)->getName();
        if (!$this->entityMetadata->isAnIliosEntity($class)) {
            throw new \Exception("Sorry. {$shortCut} is not an Ilios entity.");
        }
        $reflection = new \ReflectionClass($class);
        $entity = $reflection->getShortName();

        $propertyReflection = $this->entityMetadata->extractExposedProperties($reflection);
        $properties = array_map(function (\ReflectionProperty $property) {
            $type = $this->entityMetadata->getTypeOfProperty($property);
            if ($type === 'entity') {
                $type = 'string';
            }
            if ($type === 'entityCollection') {
                $type = 'arrayOfStrings';
            }
            return [
                'name' => $property->getName(),
                'type' => $type
            ];
        }, $propertyReflection);


        $template = 'IliosCliBundle:Swagger:definition.yml.twig';

        $content = $this->templatingEngine->render($template, [
            'entity' => $entity,
            'properties' => $properties
        ]);

        print $content; die;
    }
}
