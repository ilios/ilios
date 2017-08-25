<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UsermaterialController
 */
class UsermaterialController extends AbstractController
{
    /**
     * Get the materials for a user
     *
     * @param string $version
     * @param int $id of the user
     * @param Request $request
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserManager $manager
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function getAction(
        $version,
        $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserManager $manager,
        SerializerInterface $serializer
    ) {
        $user = $manager->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted('view', $user)) {
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

        //Un-privileged users get less data
        if (!$user->hasRole(['Faculty', 'Course Director', 'Developer'])) {
            $now = new \DateTime();
            $this->clearTimedMaterials($materials, $now);
        }


        //If there are no matches return an empty array
        $response['userMaterials'] = $materials ? array_values($materials) : [];
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }

    /**
     * @param UserMaterial[] $materials
     * @param \DateTime $dateTime
     */
    protected function clearTimedMaterials(array $materials, \DateTime $dateTime) {
        foreach ($materials as $material) {
            $material->clearTimedMaterial($dateTime);
        }
    }
}
