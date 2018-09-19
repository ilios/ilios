<?php

namespace App\Command;

use App\Service\EntityMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Generates the YAML file for the swagger definition docs
 *
 * Class GenerateSwaggerApiPathYamlCommand
 */
class GenerateSwaggerApiDefinitionYamlCommand extends Command
{
    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var EntityMetadata
     */
    protected $entityMetadata;

    /**
     *
     * @param EngineInterface $templatingEngine
     * @param RegistryInterface $registry
     * @param EntityMetadata $entityMetadata
     */
    public function __construct(
        EngineInterface $templatingEngine,
        RegistryInterface $registry,
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
                'The name of an entity e.g. AppBundle:Session.'
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

        $mapProperties = function (\ReflectionProperty $property) {
            $type = $this->entityMetadata->getTypeOfProperty($property);
            if ($type === 'entity') {
                $type = 'string';
            }
            return [
                'name' => $property->getName(),
                'readOnly' => $this->entityMetadata->isPropertyReadOnly($property),
                'type' => $type
            ];
        };
        $reflectionProperties = $this->entityMetadata->extractExposedProperties($reflection);
        $properties = array_map($mapProperties, $reflectionProperties);

        $template = 'AppBundle:Template:definition.yml.twig';

        $content = $this->templatingEngine->render($template, [
            'entity' => $entity,
            'properties' => $properties,
        ]);

        print $content;

        exit();
    }
}
