<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Service\Index\Curriculum;
use App\Service\Index\Users;
use App\Service\SessionUserPermissionChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class SearchController
 *
 * Search Ilios
 */
class SearchController extends AbstractController
{
    public function __construct(
        protected Curriculum $curriculumIndex,
        protected Users $userIndex,
        protected TokenStorageInterface $tokenStorage,
        protected SessionUserPermissionChecker $permissionChecker
    ) {
    }

    #[Route(
        '/api/search/v1/curriculum',
        methods: ['GET'],
    )]
    public function curriculumSearchV1(Request $request): Response
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        $canSearchCurriculum = false;
        if ($user instanceof SessionUserInterface) {
            $canSearchCurriculum = $this->permissionChecker->canSearchCurriculum($user);
        } elseif ($user instanceof ServiceTokenUserInterface) {
            $canSearchCurriculum = true;
        }
        if (! $canSearchCurriculum) {
            throw new AccessDeniedException();
        }

        return new Response(
            'The v1 curriculum search API has been removed, please use the v2 API',
            Response::HTTP_GONE
        );
    }

    #[Route(
        '/api/search/v2/curriculum',
        methods: ['GET'],
    )]
    public function curriculumSearch(
        #[MapQueryParameter] string $q,
        #[MapQueryParameter] int $size,
        #[MapQueryParameter] int $from,
        #[MapQueryParameter] string $schools,
        #[MapQueryParameter] string $years,
    ): JsonResponse {
        $user = $this->tokenStorage->getToken()?->getUser();

        $canSearchCurriculum = false;
        if ($user instanceof SessionUserInterface) {
            $canSearchCurriculum = $this->permissionChecker->canSearchCurriculum($user);
        } elseif ($user instanceof ServiceTokenUserInterface) {
            $canSearchCurriculum = true;
        }
        if (! $canSearchCurriculum) {
            throw new AccessDeniedException();
        }

        $schools = explode('-', $schools);
        $schools = array_filter($schools); //remove any blanks, or if an empt string is passed return and empty array
        $schools = array_map('intval', $schools);

        $years = explode('-', $years);
        $years = array_filter($years); //remove any blanks, or if an empt string is passed return and empty array
        $years = array_map('intval', $years);

        $result = $this->curriculumIndex->search($q, $size, $from, $schools, $years);

        return new JsonResponse(['results' => $result]);
    }

    #[Route(
        '/api/search/v1/users',
        methods: ['GET'],
    )]
    public function userSearch(Request $request): JsonResponse
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        $canSearchUsers = false;
        if ($user instanceof SessionUserInterface) {
            $canSearchUsers = $this->permissionChecker->canSearchUsers($user);
        } elseif ($user instanceof ServiceTokenUserInterface) {
            $canSearchUsers = true;
        }
        if (! $canSearchUsers) {
            throw new AccessDeniedException();
        }

        $query = $request->get('q');

        $onlySuggest = (bool) $request->get('onlySuggest');
        $size = $request->get('size');

        if ($size === null) {
            $size = 100;
        } else {
            $size = (int) $size;
        }

        $result = $this->userIndex->search($query, $size, $onlySuggest);

        return new JsonResponse(['results' => $result]);
    }
}
