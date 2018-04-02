<?php

namespace Ilios\WebBundle\Controller;

use Ilios\AuthenticationBundle\Classes\PermissionMatrixInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Service\Directory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DirectoryController
 */
class DirectoryController extends Controller
{

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var Directory
     */
    protected $directory;

    /**
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * DirectoryController constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param UserManager $userManager
     * @param Directory $directory
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserManager $userManager,
        Directory $directory,
        PermissionChecker $permissionChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->directory = $directory;
        $this->permissionChecker = $permissionChecker;
    }

    public function searchAction(Request $request)
    {
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (! $this->permissionChecker->canCreateUsersInAnySchool($sessionUser)) {
            throw new AccessDeniedException();
        }
        $results = [];

        if ($request->query->has('searchTerms')) {
            $searchTerms = explode(' ', $request->query->get('searchTerms'));

            $searchResults = $this->directory->find($searchTerms);

            if (is_array($searchResults)) {
                $results = $searchResults;
            }
        }
        $offset = $request->query->has('offset')?$request->query->get('offset'):0;
        $limit = $request->query->has('limit')?$request->query->get('limit'):count($results);
        $results = array_slice($results, $offset, $limit);

        $campusIds = array_map(function ($arr) {
            return $arr['campusId'];
        }, $results);
        $dtos = $this->userManager->findAllMatchingDTOsByCampusIds($campusIds);

        $usersIdsByCampusId = [];
        foreach ($dtos as $dto) {
            $usersIdsByCampusId[$dto->campusId] = $dto->id;
        }

        $results = array_map(function ($arr) use ($usersIdsByCampusId) {
            $arr['user'] = !empty($usersIdsByCampusId[$arr['campusId']])?$usersIdsByCampusId[$arr['campusId']]:null;

            return $arr;
        }, $results);

        return new JsonResponse(array('results' => $results));
    }

    public function findAction($id)
    {
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if (!$this->permissionChecker->canCreateUsersInAnySchool($sessionUser)) {
            throw new AccessDeniedException();
        }

        $user = $this->userManager->findOneBy(['id' => $id]);
        if (! $user) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $userRecord = $this->directory->findByCampusId($user->getCampusId());
        if (!$userRecord) {
            throw new \Exception('Unable to find ' . $user->getCampusId() . ' in the directory.');
        }

        return new JsonResponse(array('result' => $userRecord));
    }
}
