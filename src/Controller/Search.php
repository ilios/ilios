<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\SessionUserInterface;
use App\Service\Index\Curriculum;
use App\Service\Index\Users;
use App\Service\PermissionChecker;
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
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * @var Users
     */
    protected $userIndex;

    /**
     * @var Curriculum
     */
    protected $curriculumIndex;

    public function __construct(
        Curriculum $curriculumIndex,
        Users $userIndex,
        TokenStorageInterface $tokenStorage,
        PermissionChecker $permissionChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->permissionChecker = $permissionChecker;
        $this->userIndex = $userIndex;
        $this->curriculumIndex = $curriculumIndex;
    }

    public function curriculumSearch(Request $request)
    {
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (! $this->permissionChecker->canSearchCurriculum($sessionUser)) {
            throw new AccessDeniedException();
        }

        $query = $request->get('q');

        $onlySuggest = (bool) $request->get('onlySuggest');

        $result = $this->curriculumIndex->search($query, $onlySuggest);

        return new JsonResponse(['results' => $result]);
    }

    public function userSearch(Request $request)
    {
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (! $this->permissionChecker->canSearchUsers($sessionUser)) {
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
