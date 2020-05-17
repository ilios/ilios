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

    public function build(string $object, array $values): Response
    {
        return new Response(
            $this->serializer->serialize(
                [ $this->endpointResponseNamer->getPluralName($object) => $values],
                'json'
            ),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
