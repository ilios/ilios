<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\BaseManager;
use Ilios\CoreBundle\Entity\Manager\DTOManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Serializer\Serializer;

class ApiController extends Controller implements ApiControllerInterface
{
    public function getAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $dto = $manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $this->resultsToResponse([$dto], $object, Response::HTTP_OK);
    }

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

        return $this->resultsToResponse($result, $object, Response::HTTP_OK);
    }

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

        return $this->createResponse($object, $entities, Response::HTTP_CREATED);
    }

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
        $serializer->deserialize($json, get_class($entity), 'json', array('object_to_populate' => $entity));
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, true, false);
        $singularName = $this->getSingularObjectName($object);

        return $this->createResponse($singularName, $entity, $code);
    }

    public function deleteAction($version, $object, $id, Request $request)
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
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * @param string $object
     * @return DTOManagerInterface
     * @throws \Exception
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
     * @param Request $request
     * @param $object string the name of the object we are extracting from the request
     * @param bool $singleItem forces items out of an array and into single items
     * @param bool $returnArray should we leave the data as an array (for further upstream processig)
     * @return string | array
     */
    protected function extractDataFromRequest(Request $request, $object, $singleItem = false, $returnArray = false)
    {
        $data = false;
        $singularName = $this->getSingularObjectName($object);
        $str = $request->getContent();
        $obj = json_decode($str);
        if (property_exists($obj, $singularName)) {
            $data = [$obj->$singularName];
        }
        if (!$data) {
            if (property_exists($obj, $object)) {
                $data = $obj->$object;
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

    protected function getSingularObjectName($object)
    {
        Inflector::rules('singular', array(
            'uninflected' => array('aamcpcrs'),
        ));
        return Inflector::singularize($object);
    }

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

    protected function validateAndAuthorizeEntities($entities, $permission)
    {
        foreach ($entities as $entity) {
            $this->validateEntity($entity);
            $this->authorizeEntity($entity, $permission);
        }
    }

    protected function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
    }

    protected function authorizeEntity($entity, $permission)
    {
        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted($permission, $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return $this->get('serializer');
    }
}
