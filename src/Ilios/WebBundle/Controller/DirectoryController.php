<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DirectoryController
 * @package Ilios\WebBundle\Controller
 */
class DirectoryController extends Controller
{
    public function searchAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user->hasRole(['Developer'])) {
            throw new AccessDeniedException();
        }
        $results = [];

        if ($request->query->has('searchTerms')) {
            $searchTerms = explode(' ', $request->query->get('searchTerms'));

            $directory = $this->container->get('ilioscore.directory');
            $searchResults = $directory->find($searchTerms);

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
        $userManager = $this->get('ilioscore.user.manager');
        $dtos = $userManager->findAllMatchingDTOsByCampusIds($campusIds);

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
}
