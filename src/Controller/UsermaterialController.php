<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\SessionUserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Classes\UserMaterial;
use App\Entity\LearningMaterialStatusInterface;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
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
     */
    #[Route(
        '/api/{version<v3>}/usermaterials/{id}',
        requirements: [
            'id' => '\d+',
        ],
        methods: ['GET'],
    )]
    public function getMaterials(
        string $version,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage
    ): Response {
        /** @var UserInterface $user */
        $user = $userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::VIEW, $user)) {
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

        $materials = $userRepository->findMaterialsForUser($user->getId(), $criteria);

        $materials = array_filter(
            $materials,
            fn($entity) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $entity)
        );

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        // Remove all draft data when not viewing your own events
        // or if the requesting user does not have elevated privileges
        $hasElevatedPrivileges = $sessionUser->isRoot() || $sessionUser->performsNonLearnerFunction();
        if ($sessionUser->getId() !== $user->getId() || ! $hasElevatedPrivileges) {
            $now = new DateTime();
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
     */
    protected function clearTimedMaterials(array $materials, DateTime $dateTime)
    {
        foreach ($materials as $material) {
            $material->clearTimedMaterial($dateTime);
        }
    }

    /**
     * @param UserMaterial[] $materials
     */
    protected function clearDraftMaterials(array $materials): array
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
