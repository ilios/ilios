<?php

declare(strict_types=1);

namespace App\Traits\APIController;

use App\RelationshipVoter\AbstractVoter;
use App\Repository\RepositoryInterface;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

trait GetOne
{
    /**
     * Handles GET request for a single entity
     * @Route("/{id}", methods={"GET"})
     */
    public function getOne(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        Request $request
    ): Response {
        $repository = $this->getRepository();
        $dto = $repository->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $values = $authorizationChecker->isGranted(AbstractVoter::VIEW, $dto) ? [$dto] : [];

        return $builder->buildResponseForGetOneRequest($this->getEndpoint(), $values, Response::HTTP_OK, $request);
    }

    abstract protected function getRepository(): RepositoryInterface;
    abstract protected function getEndpoint(): string;
}
