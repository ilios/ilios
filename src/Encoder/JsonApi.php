<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Entity\Manager\ManagerInterface;
use App\Service\EndpointResponseNamer;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Exception;

class JsonApi implements EncoderInterface, DecoderInterface
{
    use ContainerAwareTrait;

    protected const FORMAT = 'json-api';

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    public function __construct(EndpointResponseNamer $endpointResponseNamer)
    {
        $this->endpointResponseNamer = $endpointResponseNamer;
    }

    public function decode(string $data, string $format, array $context = [])
    {
        // TODO: Implement decode() method.
    }

    public function supportsDecoding(string $format)
    {
        // TODO: Implement supportsDecoding() method.
    }

    public function encode($data, string $format, array $context = [])
    {
        $rhett = [];

        foreach ($data as $object) {
            $arr = [
                'id' => (string) $object['id'],
                'type' => $object['type'],
                'attributes' => $object['attributes'],
                'relationships' => [],
            ];
            foreach ($object['related'] as $name => $related) {
                $relatedData = [];
                $value = $related['value'];
                if (is_array($value)) {
                    foreach ($value as $id) {
                        $relatedData[] = [
                            'type' => $related['type'],
                            'id' => (string) $id
                        ];
                    }
                    $arr['relationships'][$name] = [
                        'data' => $relatedData
                    ];
                } else {
                    $arr['relationships'][$name] = [
                        'data' => [
                            'type' => $related['type'],
                            'id' => (string) $value
                        ]
                    ];
                }
            }

            $rhett[] = $arr;
        }

        return json_encode(['data' => $arr]);
    }

    public function supportsEncoding(string $format)
    {
        return self::FORMAT === $format;
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
