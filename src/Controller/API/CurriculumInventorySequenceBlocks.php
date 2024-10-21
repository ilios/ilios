<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\CurriculumInventorySequenceBlockRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OutOfRangeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use RuntimeException;

#[OA\Tag(name:'Curriculum inventory sequence blocks')]
#[Route('/api/{version<v3>}/curriculuminventorysequenceblocks')]
class CurriculumInventorySequenceBlocks extends AbstractApiController
{
    public function __construct(CurriculumInventorySequenceBlockRepository $repository)
    {
        parent::__construct($repository, 'curriculuminventorysequenceblocks');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/curriculuminventorysequenceblocks/{id}',
        summary: 'Fetch a single curriculum inventory sequence block.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single curriculum inventory sequence block.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventorySequenceBlocks',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventorySequenceBlockDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '404', description: 'Not found.'),
        ]
    )]
    public function getOne(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        Request $request
    ): Response {
        return $this->handleGetOne($version, $id, $authorizationChecker, $builder, $request);
    }

    #[Route(
        methods: ['GET']
    )]
    #[OA\Get(
        path: "/api/{version}/curriculuminventorysequenceblocks",
        summary: "Fetch all curriculum inventory sequence blocks.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(
                name: 'offset',
                description: 'Offset',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Limit results',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'order_by',
                description: 'Order by fields. Must be an array, i.e. <code>&order_by[id]=ASC&order_by[x]=DESC</code>',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                ),
                style: "deepObject"
            ),
            new OA\Parameter(
                name: 'filters',
                description: 'Filter by fields. Must be an array, i.e. <code>&filters[id]=3</code>',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                ),
                style: "deepObject"
            ),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'An array of curriculum inventory sequence blocks.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventorySequenceBlocks',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventorySequenceBlockDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return $this->handleGetAll($version, $request, $authorizationChecker, $builder);
    }

    #[Route(methods: ['POST'])]
    #[OA\Post(
        path: '/api/{version}/curriculuminventorysequenceblocks',
        summary: "Create curriculum inventory sequence blocks.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'curriculumInventorySequenceBlocks',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: CurriculumInventorySequenceBlockDTO::class)
                        )
                    ),
                ],
                type: 'object',
            )
        ),
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '201',
                description: 'An array of newly created curriculum inventory sequence blocks.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventorySequenceBlocks',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventorySequenceBlockDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '400', description: 'Bad Request Data.'),
            new OA\Response(response: '403', description: 'Access Denied.'),
        ]
    )]
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $class = $this->repository->getClass() . '[]';

        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $this->endpoint);

        foreach ($entities as $entity) {
            $this->validateAndAuthorizeEntity($entity, VoterPermissions::CREATE, $validator, $authorizationChecker);

            $this->reorderBlocksInSequenceOnOrderChange(
                0,
                $entity
            );
            $this->repository->update($entity, false);
        }
        $this->repository->flush();

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $dtos, Response::HTTP_CREATED, $request);
    }

    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{version}/curriculuminventorysequenceblocks/{id}',
        summary: 'Update or create a curriculum inventory sequence block.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'curriculumInventorySequenceBlock',
                        ref: new Model(type: CurriculumInventorySequenceBlockDTO::class),
                        type: 'object'
                    ),
                ],
                type: 'object',
            )
        ),
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'The updated curriculum inventory sequence block.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventorySequenceBlock',
                            ref: new Model(type: CurriculumInventorySequenceBlockDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created curriculum inventory sequence block.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventorySequenceBlock',
                            ref: new Model(type: CurriculumInventorySequenceBlockDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '400', description: 'Bad Request Data.'),
            new OA\Response(response: '403', description: 'Access Denied.'),
            new OA\Response(response: '404', description: 'Not Found.'),
        ]
    )]
    public function put(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $entity = $this->repository->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = VoterPermissions::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = VoterPermissions::CREATE;
        }
        $oldChildSequenceOrder = $entity->getChildSequenceOrder();
        $oldOrderInSequence = $entity->getOrderInSequence();

        /** @var CurriculumInventorySequenceBlockInterface $entity */
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateEntity($entity, $validator);
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->reorderChildrenOnChildSequenceOrderChange(
            $oldChildSequenceOrder,
            $entity
        );
        $this->reorderBlocksInSequenceOnOrderChange(
            $oldOrderInSequence,
            $entity
        );

        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, $code, $request);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        path: '/api/{version}/curriculuminventorysequenceblocks/{id}',
        summary: 'Delete a curriculum inventory sequence block.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(response: '204', description: 'Deleted.'),
            new OA\Response(response: '403', description: 'Access Denied.'),
            new OA\Response(response: '404', description: 'Not Found.'),
            new OA\Response(
                response: '500',
                description: 'Deletion failed (usually caused by non-cascading relationships).'
            ),
        ]
    )]
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->repository->findOneBy(['id' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(VoterPermissions::DELETE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        try {
            $this->reorderSiblingsOnDeletion($entity);
            $this->repository->delete($entity);
            $this->repository->flush();

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to delete entity: " . $exception->getMessage());
        }
    }

    #[Route(
        '/{id}',
        methods: ['PATCH']
    )]
    public function patch(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $type = $request->getAcceptableContentTypes();
        if (!in_array("application/vnd.api+json", $type)) {
            throw new BadRequestHttpException("PATCH is only allowed for JSON:API requests, use PUT instead");
        }

        $entity = $this->repository->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $oldChildSequenceOrder = $entity->getChildSequenceOrder();
        $oldOrderInSequence = $entity->getOrderInSequence();

        $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);
        $this->validateAndAuthorizeEntity($entity, VoterPermissions::EDIT, $validator, $authorizationChecker);

        $this->reorderChildrenOnChildSequenceOrderChange(
            $oldChildSequenceOrder,
            $entity
        );
        $this->reorderBlocksInSequenceOnOrderChange(
            $oldOrderInSequence,
            $entity
        );

        $this->repository->update($entity, true, false);

        $dtos = $this->fetchDtosForEntities([$entity]);

        return $builder->buildResponseForPatchRequest($this->endpoint, $dtos[0], Response::HTTP_OK, $request);
    }

    /**
     * Reorders siblings of the sequence block being deleted.
     */
    protected function reorderSiblingsOnDeletion(
        CurriculumInventorySequenceBlockInterface $block
    ) {
        $parent = $block->getParent();
        if (! $parent || $parent->getChildSequenceOrder() !== CurriculumInventorySequenceBlockInterface::ORDERED) {
            return;
        }

        $siblings = $parent->getChildren()->toArray();
        /** @var CurriculumInventorySequenceBlockInterface[] $siblingsWithHigherSortOrder */
        $siblingsWithHigherSortOrder = array_values(array_filter(
            $siblings,
            fn($sibling) => $sibling->getOrderInSequence() > $block->getOrderInSequence()
        ));
        for ($i = 0, $n = count($siblingsWithHigherSortOrder); $i < $n; $i++) {
            $orderInSequence = $siblingsWithHigherSortOrder[$i]->getOrderInSequence();
            $siblingsWithHigherSortOrder[$i]->setOrderInSequence($orderInSequence - 1);
            $this->repository->update($block, false, false);
        }
    }

    /**
     * Reorders child sequence blocks if the parent's child sequence order changes.
     */
    protected function reorderChildrenOnChildSequenceOrderChange(
        ?int $oldValue,
        CurriculumInventorySequenceBlockInterface $block
    ) {
        /** @var CurriculumInventorySequenceBlockInterface[] $children */
        $children = $block->getChildren()->toArray();
        if (empty($children)) {
            return;
        }

        $newValue = $block->getChildSequenceOrder();

        if ($newValue === $oldValue) {
            return;
        }

        switch ($newValue) {
            case CurriculumInventorySequenceBlockInterface::ORDERED:
                usort($children, [CurriculumInventorySequenceBlock::class, 'compareSequenceBlocksWithDefaultStrategy']);
                for ($i = 0, $n = count($children); $i < $n; $i++) {
                    $children[$i]->setOrderInSequence($i + 1);
                    $this->repository->update($children[$i], false);
                }
                break;
            case CurriculumInventorySequenceBlockInterface::UNORDERED:
            case CurriculumInventorySequenceBlockInterface::PARALLEL:
                if ($oldValue === CurriculumInventorySequenceBlockInterface::ORDERED) {
                    for ($i = 0, $n = count($children); $i < $n; $i++) {
                        $children[$i]->setOrderInSequence(0);
                        $this->repository->update($children[$i], false);
                    }
                }
                break;
            default:
                // do nothing
        }
    }

    /**
     * Reorder the entire sequence if one of the blocks changes position.
     */
    protected function reorderBlocksInSequenceOnOrderChange(
        ?int $oldValue,
        CurriculumInventorySequenceBlockInterface $block
    ) {
        $parent = $block->getParent();
        if (! $parent) {
            return;
        }
        if ($parent->getChildSequenceOrder() !== CurriculumInventorySequenceBlockInterface::ORDERED) {
            return;
        }

        $newValue = $block->getOrderInSequence();

        $blocks = $parent->getChildrenAsSortedList();

        if ($this->repository->isEntityPersisted($block)) {
            $blocks = array_filter(
                $blocks,
                fn(CurriculumInventorySequenceBlockInterface $sibling) => $sibling->getId() !== $block->getId()
            );
        }

        $blocks = array_values($blocks);

        $minRange = 1;
        $maxRange = count($blocks) + 1;
        if ($newValue < $minRange || $newValue > $maxRange) {
            throw new OutOfRangeException(
                "The given order-in-sequence value {$newValue} falls outside the range {$minRange} - {$maxRange}."
            );
        }

        if ($oldValue === $newValue) {
            return;
        }

        array_splice($blocks, $block->getOrderInSequence() - 1, 0, [$block]);
        for ($i = 0, $n = count($blocks); $i < $n; $i++) {
            /** @var CurriculumInventorySequenceBlockInterface $current */
            $current = $blocks[$i];
            $j = $i + 1;
            if ($current->getOrderInSequence() !== $j) {
                $current->setOrderInSequence($j);
                $this->repository->update($current, false, false);
            }
        }
    }
}
