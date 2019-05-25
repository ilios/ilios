<?php

namespace App\Controller;

use App\Service\PermissionChecker;
use App\Service\Search as SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class SearchController
 *
 * Search Ilios
 */
class Search extends AbstractController
{
    /**
     * @var SearchService
     */
    protected $search;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var PermissionChecker
     */
    protected $permissionChecker;

    public function __construct(
        SearchService $search,
        TokenStorageInterface $tokenStorage,
        PermissionChecker $permissionChecker
    ) {
        $this->search = $search;
        $this->tokenStorage = $tokenStorage;
        $this->permissionChecker = $permissionChecker;
    }

    public function search(Request $request)
    {
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (! $this->permissionChecker->canSearchCurriculum($sessionUser)) {
            throw new AccessDeniedException();
        }

        $query = $request->get('q');

        $result = $this->search->curriculumSearch($query);

        return new JsonResponse(['results' => $result]);
    }
}
