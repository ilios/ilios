<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\BaseManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NonDtoApiController extends ApiController
{
    public function getAction($version, $object, $id)
    {
        /** @var BaseManager $manager */
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer[$object] = [$entity];
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

        /** @var BaseManager $manager */
        $manager = $this->getManager($object);
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

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
}
