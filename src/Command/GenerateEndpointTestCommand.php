<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\EntityMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Inflector\Inflector;
use Exception;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

/**
 * Generates the test for an endpoint
 *
 * Class GenerateEndpointTestCommand
 */
#[AsCommand(
    name: 'ilios:generate:endpoint-test',
    description: 'Creates basic test for an endpoint.',
    hidden: true
)]
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

    public function __invoke(
        OutputInterface $output,
        #[Argument(
            description: 'The name of an entity e.g. App\Entity\Session.',
            name: 'entityShortcut'
        )] string $entityShortcut,
    ): int {
        $manager = $this->registry->getManagerForClass($entityShortcut);
        $class = $manager->getClassMetadata($entityShortcut)->getName();
        if (!$this->entityMetadata->isAnIliosEntity($class)) {
            throw new Exception("Sorry. {$entityShortcut} is not an Ilios entity.");
        }
        $reflection = new ReflectionClass($class);
        $entity = $reflection->getShortName();


        $mapProperties = fn(ReflectionProperty $property) => [
            'name' => $property->getName(),
            'type' => $this->entityMetadata->getTypeOfProperty($property),
        ];

        $writableProperties = $this->entityMetadata->extractWritableProperties($reflection);
        $puts = array_map($mapProperties, $writableProperties);

        $propertyReflection = $this->entityMetadata->extractExposedProperties($reflection);
        $filters = array_map($mapProperties, $propertyReflection);

        $propertyReflection = $this->entityMetadata->extractOnlyReadableProperties($reflection);
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

        return Command::SUCCESS;
    }
}
