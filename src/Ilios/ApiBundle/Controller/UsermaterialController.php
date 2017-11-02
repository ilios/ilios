<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function getAction(
        $version,
        $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserManager $manager,
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage
    ) {
        /** @var UserInterface $user */
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

        $materials = array_filter($materials, function ($entity) use ($authorizationChecker) {
            return $authorizationChecker->isGranted('view', $entity);
        });

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        //Remove all draft data when not viewing your own events
        //Un-privileged users get less data
        if ($sessionUser->getId() != $user->getId() ||
            !$sessionUser->hasRole(['Faculty', 'Course Director', 'Developer'])
        ) {
            $now = new \DateTime();
            $materials = $this->clearDraftMaterials($materials);
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
    protected function clearTimedMaterials(array $materials, \DateTime $dateTime)
    {
        foreach ($materials as $material) {
            $material->clearTimedMaterial($dateTime);
        }
    }

    /**
     * @param UserMaterial[] $materials
     *
     * @return array;
     */
    protected function clearDraftMaterials(array $materials) : array
    {
        $publishedMaterials = [];
        foreach ($materials as $material) {
            if ($material->status !== LearningMaterialStatusInterface::IN_DRAFT) {
                $publishedMaterials[] = $material;
            }
        }

        return $publishedMaterials;
    }
}
