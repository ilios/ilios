<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\SessionUserInterface;
use App\Entity\UserPreference;
use App\Repository\UserPreferenceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use function json_decode;

class PreferencesController extends AbstractController
{
    #[Route(
        '/application/preferences',
        methods: ['GET'],
    )]
    public function getPreferences(
        TokenStorageInterface $tokenStorage,
        UserPreferenceRepository $userPreferenceRepository,
    ): JsonResponse {
        $token = $tokenStorage->getToken();
        $sessionUser = $token?->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw $this->createAccessDeniedException('Unauthorized access.');
        }
        $json = $userPreferenceRepository->getJsonForUser($sessionUser->getId());

        if ($json === null) {
            throw $this->createNotFoundException();
        }
        return new JsonResponse(json_decode($json, false, 4));
    }

    #[Route(
        '/application/preferences',
        methods: ['PUT'],
    )]
    public function putPreferences(
        Request $request,
        TokenStorageInterface $tokenStorage,
        UserPreferenceRepository $userPreferenceRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): JsonResponse {
        $token = $tokenStorage->getToken();
        $sessionUser = $token?->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw $this->createAccessDeniedException('Unauthorized access.');
        }
        $preferences = $userPreferenceRepository->find($sessionUser->getId());

        if (!$preferences) {
            $user = $userRepository->findOneById($sessionUser->getId());
            $preferences = new UserPreference();
            $preferences->setUser($user);
        }
        $json = $request->getContent();
        $preferences->setJson($json);
        $em->persist($preferences);
        $em->flush();

        return new JsonResponse(json_decode($preferences->getJson(), false, 4));
    }
}
