<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\ApiBundle\Service\EndpointResponseNamer;
use Ilios\CoreBundle\Entity\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiController
 *
 * Default Controller for all API endpoints.
 */
class ApiController extends Controller implements ApiControllerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EndpointResponseNamer $endpointResponseNamer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->endpointResponseNamer = $endpointResponseNamer;
        $this->tokenStorage = $tokenStorage;
    }

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
            $name = ucfirst($this->getSingularResponseKey($object));
            throw new NotFoundHttpException(sprintf("%s with id '%s' was not found.", $name, $id));
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

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flush();

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
        $json = $this->extractJsonFromRequest($request, $object, 'PUT');
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

        if (! $this->authorizationChecker->isGranted('delete', $entity)) {
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
     * @param string $pluralObjectName
     *
     * @return ManagerInterface
     *
     * @throws \Exception if the manager doesn't exist or
     * if the manager does not support operations that this default
     * controller needs.
     */
    protected function getManager($pluralObjectName)
    {
        $entityName = $this->getEntityName($pluralObjectName);
        $name = "Ilios\\CoreBundle\\Entity\\Manager\\${entityName}Manager";
        if (!$this->container->has($name)) {
            throw new \Exception(
                sprintf('The manager for \'%s\' does not exist.', $pluralObjectName)
            );
        }

        $manager = $this->container->get($name);

        if (!$manager instanceof ManagerInterface) {
            $class = $manager->getClass();
            throw new \Exception("{$class} is not an Ilios Manager.");
        }

        return $manager;
    }

    /**
     * Take the request object and pull out the input data we need for a POST request
     * which can be either an object under a singular key or an array of objects
     * under a plural key
     *
     * @param Request $request
     * @param string $object we are extracting from the request
     *
     * @throws BadRequestHttpException when the key does not exist or match the data
     * @return Object[]
     */
    protected function extractPostDataFromRequest(Request $request, $object)
    {
        $data = false;
        $str = $request->getContent();
        $obj = json_decode($str);

        $singularResponseKey = $this->getSingularResponseKey($object);
        $pluralResponseKey = $this->getPluralResponseKey($object);
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
     *
     * @param Request $request
     * @param string $object we are extracting from the request
     *
     * @throws BadRequestHttpException when the key does not exist or match the data
     * @return Object
     */
    protected function extractPutDataFromRequest(Request $request, $object)
    {
        $data = false;
        $str = $request->getContent();
        $obj = json_decode($str);

        $key = $this->getSingularResponseKey($object);
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
        }

        if (!$data) {
            throw new BadRequestHttpException(
                sprintf(
                    "This request contained no usable data.  Expected to find it under %s",
                    $key
                )
            );
        }

        return $data;
    }

    /**
     * Get the JSON we need from our POST Request
     *
     * @param Request $request
     * @param string $object we are extracting from the request
     * @param string $type or request POST or PUT
     *
     * @throws \Exception when invalid $type is sent
     * @return string JSON
     */
    protected function extractJsonFromRequest(Request $request, $object, $type)
    {
        $uType = strtoupper($type);
        if (!in_array($uType, ['POST', 'PUT'])) {
            throw new \Exception("Invalid input type must be either POST or PUT you sent ${type}");
        }
        $data = null;
        if ('POST' === $uType) {
            $data = $this->extractPostDataFromRequest($request, $object);
        }
        if ('PUT' === $uType) {
            $data = $this->extractPutDataFromRequest($request, $object);
        }

        return json_encode($data);
    }

    /**
     * Get the Entity name for an endpoint
     *
     * @param string $object
     *
     * @return string
     */
    protected function getEntityName($object)
    {
        return ucfirst($this->endpointResponseNamer->getSingularName($object));
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
        return $this->endpointResponseNamer->getPluralName($object);
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
        return $this->endpointResponseNamer->getSingularName($object);
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
        $authChecker = $this->authorizationChecker;
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
     *
     * @throws HttpException when input is no valid
     * @throws AccessDeniedException when authorization is insufficient
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
     *
     * @throws HttpException when input is no valid
     */
    protected function validateEntity($entity)
    {
        $errors = $this->validator->validate($entity);
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
     *
     * @throws AccessDeniedException when authorization is insufficient
     */
    protected function authorizeEntity($entity, $permission)
    {
        if (! $this->authorizationChecker->isGranted($permission, $entity)) {
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
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }
}
