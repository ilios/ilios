<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class ApiRequestParser
{
    public function __construct(
        protected EndpointResponseNamer $endpointResponseNamer,
        protected SerializerInterface $serializer,
        protected JsonApiDataShaper $dataShaper
    ) {
    }

    /**
     * Extract the non-data parameters which control the response we send
     */
    public static function extractParameters(Request $request): array
    {
        $parameters = [
            'offset' => $request->query->has('offset') ? (int) $request->query->all()['offset'] : null,
            'limit' => $request->query->has('limit') ? (int) $request->query->all()['limit'] : null,
            'orderBy' => $request->query->has('order_by') ? $request->query->all()['order_by'] : null,
            'criteria' => []
        ];


        $criteria = $request->query->has('filters') ? $request->query->all()['filters'] : [];
        if (is_null($criteria)) {
            $criteria = [];
        }
        $criteria = array_map(function ($item) {
            //convert boolean/null strings to boolean/null values
            $item = $item === 'null' ? null : $item;
            $item = $item === 'false' ? false : $item;

            return $item === 'true' ? true : $item;
        }, $criteria);

        $parameters['criteria'] = $criteria;

        return $parameters;
    }

    /**
     * Take the request object and pull out the input data we need for a POST request
     * which can be either an object under a singular key or an array of objects
     * under a plural key
     */
    public function extractPostDataFromRequest(Request $request, string $object): array
    {
        $data = false;
        $str = $request->getContent();
        $obj = json_decode($str);

        $type = $request->getAcceptableContentTypes();
        if (in_array("application/vnd.api+json", $type)) {
            if (!property_exists($obj, 'data')) {
                throw new BadRequestHttpException(
                    "The required 'data' value was not found in request."
                );
            }

            $data = is_array($obj->data) ? $obj->data : [$obj->data];
            $dataShaper = $this->dataShaper;

            return array_map(function ($data) use ($dataShaper) {
                $shaped = $dataShaper->flattenJsonApiData($data);
                return json_decode(json_encode($shaped), false);
            }, $data);
        }


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

        return $data;
    }

    /**
     * Take the request object and pull out the input data we need for a PUT request
     * which can only be a single object under a singular key
     */
    public function extractPutDataFromRequest(Request $request, string $object): object
    {
        $str = $request->getContent();
        $obj = json_decode($str);

        $type = $request->getAcceptableContentTypes();
        if (in_array("application/vnd.api+json", $type)) {
            if (!property_exists($obj, 'data')) {
                throw new BadRequestHttpException(
                    "The required 'data' value was not found in request."
                );
            }

            return json_decode(json_encode($this->dataShaper->flattenJsonApiData($obj->data)), false);
        }


        $key = $this->endpointResponseNamer->getSingularName($object);
        if (property_exists($obj, $key)) {
            $data = $obj->$key;

            if (is_array($data)) {
                throw new BadRequestHttpException(
                    sprintf(
                        "Data was found in %s but it should be an object not an array.",
                        $key
                    )
                );
            }

            return $data;
        } else {
            throw new BadRequestHttpException(
                sprintf(
                    "This request contained no usable data.  Expected to find it under %s",
                    $key
                )
            );
        }
    }

    /**
     * Parse a POST request and return de-serialized ORM Entities
     */
    public function extractEntitiesFromPostRequest(Request $request, string $class, string $object): array
    {
        $type = $request->getAcceptableContentTypes();
        if (in_array("application/vnd.api+json", $type)) {
            $json = $request->getContent();
            return $this->serializer->deserialize($json, $class, 'json-api');
        }

        $data = $this->extractPostDataFromRequest($request, $object);
        $json = json_encode($data);

        return $this->serializer->deserialize($json, $class, 'json');
    }

    /**
     * Parse a PUT request and return de-serialized ORM Entity
     */
    public function extractEntityFromPutRequest(Request $request, object $entity, string $object): object
    {
        $type = $request->getAcceptableContentTypes();
        if (in_array("application/vnd.api+json", $type)) {
            $obj = json_decode($request->getContent());
            $data = $this->dataShaper->flattenJsonApiData($obj->data);
        } else {
            $data = $this->extractPutDataFromRequest($request, $object);
        }
        $json = json_encode($data);

        return $this->serializer->deserialize($json, $entity::class, 'json', ['object_to_populate' => $entity]);
    }
}
