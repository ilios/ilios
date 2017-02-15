<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Util\Inflector;

class ApiController extends Controller
{
    public function getAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $dto = $manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $dto)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer[$object] = [$dto];
        $serializer = $this->get('serializer');

        $response = new Response(
            $serializer->serialize($answer, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );

        return $response;
    }

    public function getAllAction($version, $object, Request $request)
    {
        $offset = $request->query->get('offset');
        $limit = !is_null($request->query->get('limit')) ? $request->query->get('limit') : 20;

        $orderBy = $request->query->get('order_by');
        $criteria = !is_null($request->query->get('filters')) ? $request->query->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);
        if (array_key_exists('updatedAt', $criteria)) {
            $criteria['updatedAt'] = new \DateTime($criteria['updatedAt']);
        }

        $manager = $this->getManager($object);
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer[$object] = $result ? array_values($result) : [];

        $serializer = $this->get('serializer');

        $response = new Response(
            $serializer->serialize($answer, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );

        return $response;
    }

    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $data = $this->extractDataFromRequest($request, $object);
        $serializer = $this->container->get('serializer');

        $entity = $manager->create();
        $serializer->deserialize($data, get_class($entity), 'json', array('object_to_populate' => $entity));

        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response(
                $errorsString,
                Response::HTTP_BAD_REQUEST
            );
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('create', $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $manager->update($entity, true, false);

        $answer[$object] = [$entity];

        $response = new Response(
            $serializer->serialize($answer, 'json'),
            Response::HTTP_CREATED,
            ['Content-type' => 'application/json']
        );

        return $response;
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

        $data = $this->extractDataFromRequest($request, $object);
        $serializer = $this->get('serializer');

        $serializer->deserialize($data, get_class($entity), 'json', array('object_to_populate' => $entity));
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response(
                $errorsString,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($id != (string) $entity) {
            return new Response(
                'The URL ID and the provided data ID do not match',
                Response::HTTP_BAD_REQUEST
            );
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted($permission, $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $manager->update($entity, true, false);
        $singularName = $this->getSingularObjectName($object);
        $answer[$singularName] = $entity;

        $response = new Response(
            $serializer->serialize($answer, 'json'),
            $code,
            ['Content-type' => 'application/json']
        );

        return $response;
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

    protected function getManager($object)
    {
        $singularName = $this->getSingularObjectName($object);
        $name = "ilioscore.{$singularName}.manager";
        if (!$this->container->has($name)) {
            throw new \Exception(
              "There is no manager for ${object}.  Tried {$name}."
            );
        }
        return $this->container->get($name);

    }

    protected function extractDataFromRequest(Request $request, $object)
    {
        $singularName = $this->getSingularObjectName($object);
        $data = $request->request->get($object);
        if (!$data) {
            $str = $request->getContent();
            $obj = json_decode($str);
            $block = $obj->$singularName;
            $data = json_encode($block);
        }
        if (!$data) {
            $data = $request->getContent();
        }

        return $data;
    }

    protected function getSingularObjectName($object)
    {
        return Inflector::singularize($object);
    }
}
