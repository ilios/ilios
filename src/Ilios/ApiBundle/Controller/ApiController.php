<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\DTOManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ApiController
 *
 * Default Controller for all API endpoints.
 * @package Ilios\ApiBundle\Controller
 */
class ApiController extends Controller implements ApiControllerInterface
{
    /**
     * Handles single GET requests for endpoints
     *
     * @param string $version the API version (v1) requested
     * @param string $object the name of the endpoint
     * @param mixed $id the ID of the requested entity
     *
     * @return Response
     */
    public function getAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $dto = $manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $this->resultsToResponse([$dto], $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    /**
     * Handles plural GET requests for endpoints
     *
     * @param string $version the API version (v1) requested
     * @param string $object the name of the endpoint
     * @param Request $request details (filters, order, limit)
     *
     * @return Response
     */
    public function getAllAction($version, $object, Request $request)
    {
        $parameters = $this->extractParameters($request);
        $manager = $this->getManager($object);
        $result = $manager->findDTOsBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );

        return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    /**
     * Handles POST which creates new data in the API
     *
     * @param string $version the API version (v1) requested
     * @param string $object the name of the endpoint
     *
     * @param Request $request the content to be created
     *
     * @return Response
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractDataFromRequest($request, $object);
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flushAndClear();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     *
     * @param string $version the API version (v1) requested
     * @param string $object the name of the endpoint
     * @param string $id the ID of the entity we are modifying
     *
     * @param Request $request the content to be modified
     * @return Response
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id'=> $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }

        $json = $this->extractDataFromRequest($request, $object, $singleItem = true);
        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, true, false);

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    /**
     * Handles DELETE requests to remove an element from the API
     *
     * @param string $version the API version (v1) requested
     * @param string $object the name of the endpoint
     * @param string $id the entity to be deleted
     *
     * @throws \RuntimeException when there is a problem deleting something
     *
     * @return Response
     */
    public function deleteAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id'=> $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager->delete($entity);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Failed to delete entity: " . $exception->getMessage());
        }
    }

    /**
     * Get the manager for this request by name
     *
     * @param string $object
     *
     * @return DTOManagerInterface
     *
     * @throws \Exception if the manager doesn't exist or
     * if the manager does not support operations that this default
     * controller needs.
     */
    protected function getManager($object)
    {
        $singularName = $this->getSingularObjectName($object);
        $name = "ilioscore.{$singularName}.manager";
        if (!$this->container->has($name)) {
            throw new \Exception(
                sprintf('The manager for \'%s\' does not exist.', $object)
            );
        }

        $manager = $this->container->get($name);

        if (!$manager instanceof DTOManagerInterface) {
            $class = $manager->getClass();
            throw new \Exception("{$class} is not DTO enabled.");
        }

        return $manager;
    }

    /**
     * Take the request object and pull out the input data we need
     *
     * @param Request $request
     * @param $object string the name of the object we are extracting from the request
     * @param bool $singleItem forces items out of an array and into single items
     * @param bool $returnArray should we leave the data as an array (for further upstream processing)
     *
     * @return mixed
     */
    protected function extractDataFromRequest(Request $request, $object, $singleItem = false, $returnArray = false)
    {
        $data = false;
        $singularResponseKey = $this->getSingularResponseKey($object);
        $pluralResponseKey = $this->getPluralResponseKey($object);
        $str = $request->getContent();
        $obj = json_decode($str);
        if (property_exists($obj, $singularResponseKey)) {
            $data = [$obj->$singularResponseKey];
        }
        if (!$data) {
            if (property_exists($obj, $pluralResponseKey)) {
                $data = $obj->$pluralResponseKey;
            }
        }
        if (!$data) {
            $data = [$obj];
        }

        if ($singleItem) {
            $data = array_shift($data);
        }

        return $returnArray?$data:json_encode($data);
    }

    /**
     * Get the singular name of an endpoint
     *
     * @param string $object
     *
     * @return string
     */
    protected function getSingularObjectName($object)
    {
        $namer = $this->container->get('ilios_api.endpoint_response_namer');

        return strtolower($namer->getSingularName($object));
    }

    /**
     * Get the plural name of an endpoint
     *
     * @param string $object
     *
     * @return string
     */
    protected function getPluralResponseKey($object)
    {
        $namer = $this->container->get('ilios_api.endpoint_response_namer');

        return $namer->getPluralName($object);
    }

    /**
     * Get the singular name of the responseKey we will
     * send with data at this endpoint
     *
     * @param string $object
     *
     * @return string
     */
    protected function getSingularResponseKey($object)
    {
        $namer = $this->container->get('ilios_api.endpoint_response_namer');

        return $namer->getSingularName($object);
    }

    /**
     * Extract the non-data parameters which control the response we send
     *
     * @param Request $request
     *
     * @return array
     */
    protected function extractParameters(Request $request)
    {
        $parameters = [
            'offset' => $request->query->get('offset'),
            'limit' => !is_null($request->query->get('limit')) ? $request->query->get('limit') : 20,
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
     * Convert the results we have generated into a Response which
     * Symfony can use to present data to the user
     *
     * @param array $results
     * @param string $responseKey the key we will send this data under
     * @param string $responseCode the HTTP status code we will use with the request
     *
     * @return Response
     */
    protected function resultsToResponse(array $results, $responseKey, $responseCode)
    {
        $authChecker = $this->get('security.authorization_checker');
        $filteredResults = array_filter($results, function ($object) use ($authChecker) {
            return $authChecker->isGranted('view', $object);
        });

        //If there are no matches return an empty array
        //If there are matches then re-index the array
        $values = !empty($filteredResults) ? array_values($filteredResults) : [];

        return $this->createResponse($responseKey, $values, $responseCode);
    }

    /**
     * Create a response object
     *
     * @param string $responseKey the key we will send this data under
     * @param mixed $value
     * @param string $responseCode the HTTP status code we will use with the request
     * @return Response
     */
    protected function createResponse($responseKey, $value, $responseCode)
    {
        $response[$responseKey] = $value;
        $serializer = $this->getSerializer();
        return new Response(
            $serializer->serialize($response, 'json'),
            $responseCode,
            ['Content-type' => 'application/json']
        );
    }

    /**
     * Validate and authorize a set of entities
     *
     * @param array $entities
     * @param string $permission we want to authorize ('view', 'create', 'edit', 'delete')
     */
    protected function validateAndAuthorizeEntities($entities, $permission)
    {
        foreach ($entities as $entity) {
            $this->validateEntity($entity);
            $this->authorizeEntity($entity, $permission);
        }
    }

    /**
     * Runs the Symfony validation against the annotations in an entity
     *
     * @param mixed $entity
     */
    protected function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
    }

    /**
     * Checks that the current user has the permissions
     * they need to work with an entity
     *
     * @param mixed $entity
     * @param string $permission we need to verify
     */
    protected function authorizeEntity($entity, $permission)
    {
        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted($permission, $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }
    }

    /**
     * Gets the serializer service
     *
     * Currently returns the default serializer, but if we need
     * to build a custom implementation this will make it easier to use it
     * in this controller
     *
     * @return Serializer
     */
    protected function getSerializer()
    {
        return $this->get('serializer');
    }
}
