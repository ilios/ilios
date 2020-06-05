<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshDescriptorManager;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/api/{version<v1|v2>}/meshdescriptors")
 */
class MeshDescriptors extends ReadOnlyController
{
    /**
     * @var MeshDescriptorManager
     */
    protected $manager;

    public function __construct(MeshDescriptorManager $manager)
    {
        parent::__construct($manager, 'meshdescriptors');
    }

    /**
     * Handle the special 'q' parameter
     * @Route("", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $q = $request->get('q');
        $parameters = ApiRequestParser::extractParameters($request);

        if (null !== $q && '' !== $q) {
            $dtos = $this->manager->findMeshDescriptorDtosByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            $filteredResults = array_filter($dtos, function ($object) use ($authorizationChecker) {
                return $authorizationChecker->isGranted(AbstractVoter::VIEW, $object);
            });

            //Re-index numerically index the array
            $values = array_values($filteredResults);

            return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
        }

        return parent::getAll($version, $request, $authorizationChecker, $builder);
    }
}
