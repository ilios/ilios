<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Manager\ManagerInterface;
use App\Service\EndpointResponseNamer;
use App\Service\EntityMetadata;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ReflectionClass;
use ReflectionProperty;
use DateTime;
use Exception;

class JsonApiDTO implements NormalizerInterface
{

    /**
     * @var EntityMetadata
     */
    protected $entityMetadata;

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(
        ContainerInterface $container,
        EntityMetadata $entityMetadata,
        EndpointResponseNamer $endpointResponseNamer
    ) {
        $this->entityMetadata = $entityMetadata;
        $this->endpointResponseNamer = $endpointResponseNamer;
        $this->container = $container;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $reflection = new ReflectionClass($object);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        $attributes = [];
        foreach ($exposedProperties as $property) {
            $attributes[$property->name] = $this->getPropertyValue($property, $object);
        }

        $relatedProperties = $this->entityMetadata->extractRelated($reflection);

        $type = $this->entityMetadata->extractType($reflection);
        $idProperty = $this->entityMetadata->extractId($reflection);
        $id = $attributes[$idProperty];
        unset($attributes[$idProperty]);

        $related = [];
        foreach ($relatedProperties as $attributeName => $relationshipType) {
            $value = $attributes[$attributeName];
            if ($value) {
                $related[$attributeName] = [
                    'type' => $relationshipType,
                    'value' => $value,
                ];
            }

            unset($attributes[$attributeName]);
        }

        $included = [];
        if (array_key_exists('include', $context)) {
            $fields = explode(',', $context['include']);

            foreach ($fields as $str) {
                $parts = explode('.', $str);
                $key = array_shift($parts);
                if (array_key_exists($key, $related)) {
                    $foo = $related[$key];
                    if (is_array($foo['value'])) {
                        $manager = $this->getManager($foo['type']);
                        $dtos = $manager->findDTOsBy(['id' => $foo['value']]);
                        foreach ($dtos as $dto) {
                            $newIncludes = $this->pullInclude(
                                $this->normalize($dto, $format, ['include' => implode('.', $parts)])
                            );
                            $included = array_merge($included, $newIncludes);
                        }
                    } else {
                        $manager = $this->getManager($foo['type']);
                        $dto = $manager->findDTOBy(['id' => $foo['value']]);
                        $newIncludes = $this->pullInclude(
                            $this->normalize($dto, $format, ['include' => implode('.', $parts)])
                        );
                        $included = array_merge($included, $newIncludes);
                    }
                }
            }
        }

        return [
            'id' => $id,
            'type' => $type,
            'attributes' => $attributes,
            'related' => $related,
            'included' => $included,
        ];
    }

    protected function pullInclude(array $arr): array
    {
        $rhett = [
            [
                'id' => $arr['id'],
                'type' => $arr['type'],
                'attributes' => $arr['attributes'],
            ]
        ];
        if (array_key_exists('included', $arr)) {
            foreach ($arr['included'] as $inc) {
                $rhett = array_merge($rhett, $this->pullInclude($inc));
            }
        }

        return $rhett;
    }

    protected function getPropertyValue(ReflectionProperty $property, object $object)
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if ($type === 'string') {
            $value = $object->{$property->name};
            return null === $value ? null : (string) $value;
        }

        if ($type === 'dateTime') {
            /** @var DateTime $value */
            $value = $object->{$property->name};
            return null === $value ? null : $value->format('c');
        }

        if ($type === 'array<string>') {
            $values = $object->{$property->name};
            $stringValues = array_map('strval', $values);

            return array_values($stringValues);
        }

        return $object->{$property->name};
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $format === 'json-api' && $this->entityMetadata->isAnIliosDto($data);
    }

    /**
     * Get the Entity name for an endpoint
     *
     */
    protected function getEntityName(string $name): string
    {
        return ucfirst($this->endpointResponseNamer->getSingularName($name));
    }

    /**
     * Get the manager for this request by name
     */
    protected function getManager(string $pluralObjectName): ManagerInterface
    {
        $entityName = $this->getEntityName($pluralObjectName);
        $name = "App\\Entity\\Manager\\${entityName}Manager";
        if (!$this->container->has($name)) {
            throw new Exception(
                sprintf('The manager for \'%s\' does not exist.', $pluralObjectName)
            );
        }

        $manager = $this->container->get($name);

        if (!$manager instanceof ManagerInterface) {
            $class = $manager->getClass();
            throw new Exception("{$class} is not an Ilios Manager.");
        }

        return $manager;
    }
}
