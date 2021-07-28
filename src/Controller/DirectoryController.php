<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\PermissionChecker;
use App\Service\Directory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DirectoryController
 */
class DirectoryController extends AbstractController
{

    public function __construct(
        protected TokenStorageInterface $tokenStorage,
        protected UserRepository $userRepository,
        protected Directory $directory,
        protected PermissionChecker $permissionChecker
    ) {
    }

    public function searchAction(Request $request)
    {
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (! $this->permissionChecker->canCreateUsersInAnySchool($sessionUser)) {
            throw new AccessDeniedException();
        }
        $results = [];

        if ($request->query->has('searchTerms')) {
            $searchTerms = explode(' ', $request->query->all()['searchTerms']);

            $searchResults = $this->directory->find($searchTerms);

            if (is_array($searchResults)) {
                $results = $searchResults;
            }
        }
        $offset = $request->query->has('offset') ? (int) $request->query->all()['offset'] : 0;
        $limit = $request->query->has('limit') ? (int) $request->query->all()['limit'] : count($results);
        $results = array_slice($results, $offset, $limit);

        $campusIds = array_map(fn($arr) => $arr['campusId'], $results);
        $dtos = $this->userRepository->findAllMatchingDTOsByCampusIds($campusIds);

        $usersIdsByCampusId = [];
        foreach ($dtos as $dto) {
            $usersIdsByCampusId[$dto->campusId] = $dto->id;
        }

        $results = array_map(function ($arr) use ($usersIdsByCampusId) {
            $arr['user'] = !empty($usersIdsByCampusId[$arr['campusId']]) ? $usersIdsByCampusId[$arr['campusId']] : null;

            return $arr;
        }, $results);

        return new JsonResponse(['results' => $results]);
    }

    public function findAction($id)
    {
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (!$this->permissionChecker->canCreateUsersInAnySchool($sessionUser)) {
            throw new AccessDeniedException();
        }

        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (! $user) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $userRecord = $this->directory->findByCampusId($user->getCampusId());
        if (!$userRecord) {
            throw new \Exception('Unable to find ' . $user->getCampusId() . ' in the directory.');
        }

        return new JsonResponse(['result' => $userRecord]);
    }
}
