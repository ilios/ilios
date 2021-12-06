<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\EntityMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Inflector\Inflector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

/**
 * Generates the test for an endpoint
 *
 * Class GenerateEndpointTestCommand
 */
class GenerateEndpointTestCommand extends Command
{
    public function __construct(
        protected Environment $twig,
        protected ManagerRegistry $registry,
        protected EntityMetadata $entityMetadata,
        protected Inflector $inflector
    ) {
        parent::__construct();
    }

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shortCut = $input->getArgument('entityShortcut');

        $manager = $this->registry->getManagerForClass($shortCut);
        $class = $manager->getClassMetadata($shortCut)->getName();
        if (!$this->entityMetadata->isAnIliosEntity($class)) {
            throw new \Exception("Sorry. {$shortCut} is not an Ilios entity.");
        }
        $reflection = new \ReflectionClass($class);
        $entity = $reflection->getShortName();


        $mapProperties = fn(\ReflectionProperty $property) => [
            'name' => $property->getName(),
            'type' => $this->entityMetadata->getTypeOfProperty($property)
        ];

        $writableProperties = $this->entityMetadata->extractWritableProperties($reflection);
        $puts = array_map($mapProperties, $writableProperties);

        $propertyReflection = $this->entityMetadata->extractExposedProperties($reflection);
        $filters = array_map($mapProperties, $propertyReflection);

        $propertyReflection = $this->entityMetadata->extractReadOnlyProperties($reflection);
        $readOnlies = array_map($mapProperties, $propertyReflection);

        $plural = $this->inflector->pluralize($entity);
        $endpoint = strtolower($plural);
        $template = 'generate/endpointTest.php.twig';
        $groupNumber = rand(1, 2);

        $content = $this->twig->render($template, [
            'entity' => $entity,
            'plural' => $plural,
            'endpoint' => $endpoint,
            'filters' => $filters,
            'puts' => $puts,
            'readOnlies' => $readOnlies,
            'groupNumber' => $groupNumber,
        ]);

        print $content;

        return 0;
    }
}
