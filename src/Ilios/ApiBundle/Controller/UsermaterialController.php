<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;

/**
 * Class UsermaterialController
 * @package Ilios\ApiBundle\Controller
 */
class UsermaterialController extends Controller
{
    /**
     * Get the materials for a user
     * @param string $version of the API requested
     * @param int $id of the user
     * @param Request $request
     *
     * @return Response
     */
    public function getAction($version, $id, Request $request)
    {
        $manager = $this->container->get('ilioscore.user.manager');

        $user = $manager->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $criteria = [];
        $beforeTimestamp = $request->get('before');
        if (!is_null($beforeTimestamp)) {
            $criteria['before'] = DateTime::createFromFormat('U', $beforeTimestamp);
        }
        $afterTimestamp = $request->get('after');
        if (!is_null($afterTimestamp)) {
            $criteria['after'] = DateTime::createFromFormat('U', $afterTimestamp);
        }

        $materials = $manager->findMaterialsForUser($user->getId(), $criteria);

        //If there are no matches return an empty array
        $response['userMaterials'] = $materials ? array_values($materials) : [];
        $serializer = $this->get('ilios_api.serializer');
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
