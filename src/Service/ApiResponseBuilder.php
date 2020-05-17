<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResponseBuilder
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    public function __construct(
        SerializerInterface $serializer,
        EndpointResponseNamer $endpointResponseNamer
    ) {
        $this->serializer = $serializer;
        $this->endpointResponseNamer = $endpointResponseNamer;
    }

    public function buildPluralResponse(string $object, array $values, int $status): Response
    {
        return new Response(
            $this->serializer->serialize(
                [ $this->endpointResponseNamer->getPluralName($object) => $values],
                'json'
            ),
            $status,
            ['Content-type' => 'application/json']
        );
    }

    public function buildSingularResponse(string $object, object $value, int $status): Response
    {
        return new Response(
            $this->serializer->serialize(
                [ $this->endpointResponseNamer->getSingularName($object) => $value],
                'json'
            ),
            $status,
            ['Content-type' => 'application/json']
        );
    }
}
