<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class ApiRequestParser
{

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(
        EndpointResponseNamer $endpointResponseNamer,
        SerializerInterface $serializer
    ) {
        $this->endpointResponseNamer = $endpointResponseNamer;
        $this->serializer = $serializer;
    }

    /**
     * Extract the non-data parameters which control the response we send
     */
    public static function extractParameters(Request $request): array
    {
        $parameters = [
            'offset' => $request->query->get('offset'),
            'limit' => $request->query->get('limit'),
            'orderBy' => $request->query->get('order_by'),
            'criteria' => []
        ];

        $criteria = !is_null($request->query->get('filters')) ? $request->query->get('filters') : [];
        $criteria = array_map(function ($item) {
            //convert boolean/null strings to boolean/null values
            $item = $item === 'null' ? null : $item;
            $item = $item === 'false' ? false : $item;
            $item = $item === 'true' ? true : $item;

            return $item;
        }, $criteria);

        $parameters['criteria'] = $criteria;

        return $parameters;
    }

    /**
     * Take the request object and pull out the input data we need for a POST request
     * which can be either an object under a singular key or an array of objects
     * under a plural key
     */
    protected function extractPostDataFromRequest(Request $request, string $object): string
    {
        $data = false;
        $str = $request->getContent();
        $obj = json_decode($str);

        $singularResponseKey = $this->endpointResponseNamer->getSingularName($object);
        $pluralResponseKey = $this->endpointResponseNamer->getPluralName($object);
        if (property_exists($obj, $singularResponseKey)) {
            $data = $obj->$singularResponseKey;

            if (is_array($data)) {
                throw new BadRequestHttpException(
                    sprintf(
                        "Data under the singular key %s should be an object not an array.",
                        $singularResponseKey
                    )
                );
            }
            $data = [$data];
        }

        if (!$data) {
            if (property_exists($obj, $pluralResponseKey)) {
                $data = $obj->$pluralResponseKey;

                if (!is_array($data)) {
                    throw new BadRequestHttpException(
                        sprintf(
                            "Data under the plural key %s should be an array not an object.",
                            $singularResponseKey
                        )
                    );
                }
            }
        }

        if (!$data) {
            throw new BadRequestHttpException(
                sprintf(
                    "This request contained no usable data.  Expected to find it under %s or %s",
                    $pluralResponseKey,
                    $singularResponseKey
                )
            );
        }

        return json_encode($data);
    }

    /**
     * Parse a post request and return de-serialized ORM Entities
     */
    public function extractEntitiesFromPostRequest(Request $request, string $class, string $object): array
    {
        $json = $this->extractPostDataFromRequest($request, $object);
        return $this->serializer->deserialize($json, $class, 'json');
    }
}
