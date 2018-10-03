<?php

namespace App\Command;

use Doctrine\Common\Inflector\Inflector;
use App\Service\EntityMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Generates the test for an endpoint
 *
 * Class GenerateEndpointTestCommand
 */
class GenerateEndpointTestCommand extends Command
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
            ->setName('ilios:generate:endpoint-test')
            ->setHidden(true)
            ->setDescription('Creates basic test for an endpoint.')
            ->addArgument(
                'entityShortcut',
                InputArgument::REQUIRED,
                'The name of an entity e.g. App\Entity\Session.'
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
            return [
                'name' => $property->getName(),
                'type' => $this->entityMetadata->getTypeOfProperty($property)
            ];
        };

        $writableProperties = $this->entityMetadata->extractWritableProperties($reflection);
        $puts = array_map($mapProperties, $writableProperties);

        $propertyReflection = $this->entityMetadata->extractExposedProperties($reflection);
        $filters = array_map($mapProperties, $propertyReflection);

        $propertyReflection = $this->entityMetadata->extractReadOnlyProperties($reflection);
        $readOnlies = array_map($mapProperties, $propertyReflection);

        $plural = Inflector::pluralize($entity);
        $endpoint = strtolower($plural);
        $template = 'generate/endpointTest.php.twig';
        $groupNumber = rand(1, 2);

        $content = $this->templatingEngine->render($template, [
            'entity' => $entity,
            'plural' => $plural,
            'endpoint' => $endpoint,
            'filters' => $filters,
            'puts' => $puts,
            'readOnlies' => $readOnlies,
            'groupNumber' => $groupNumber,
        ]);

        print $content;

        exit();
    }
}
