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

        return new JsonResponse(array('results' => $results));
    }
}
