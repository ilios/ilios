<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Classes\UserMaterial;
use App\Classes\VoterPermissions;
use App\Entity\LearningMaterialStatusInterface;
use App\Repository\UserRepository;
use App\Traits\ApiAccessValidation;
use DateTime;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UsermaterialController
 */
#[OA\Tag(name:'User materials')]
class UsermaterialController extends AbstractController
{
    use ApiAccessValidation;

    public function __construct(protected TokenStorageInterface $tokenStorage)
    {
    }

    #[Route(
        '/api/{version<v3>}/usermaterials/{id}',
        requirements: [
            'id' => '\d+',
        ],
        methods: ['GET'],
    )]
    #[OA\Get(
        path: "/api/{version}/usermaterials/{id}",
        summary: "Fetch all materials for a given user.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'User ID', in: 'path'),
            new OA\Parameter(
                name: 'before',
                description: 'Find materials before date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'before',
                description: 'Find materials before date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'An array of user materials.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'userMaterials',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: UserMaterial::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '403', description: 'Access Denied.'),
            new OA\Response(response: '404', description: 'Not Found.'),
        ]
    )]
    public function getMaterials(
        string $version,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserRepository $userRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->validateCurrentUserAsSessionUser();

        $user = $userRepository->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(VoterPermissions::VIEW, $user)) {
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
            fn($entity) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $entity)
        );

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();

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
    protected function clearTimedMaterials(array $materials, DateTime $dateTime): void
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
