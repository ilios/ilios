<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\VoterPermissions;
use App\Entity\CohortInterface;
use App\Entity\DTO\ProgramYearDTO;
use App\Entity\ProgramYearInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\CohortRepository;
use App\Repository\ProgramYearRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Program years')]
#[Route('/api/{version<v3>}/programyears')]
class ProgramYears extends AbstractApiController
{
    public function __construct(
        protected ProgramYearRepository $programYearRepository,
        protected CohortRepository $cohortRepository,
        protected SerializerInterface $serializer
    ) {
        parent::__construct($programYearRepository, 'programyears');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/programyears/{id}',
        summary: 'Fetch a single program year.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single program year.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'programYears',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: ProgramYearDTO::class)
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
        path: "/api/{version}/programyears",
        summary: "Fetch all program years.",
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
                description: 'An array of program years.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'programYears',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: ProgramYearDTO::class)
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
        path: '/api/{version}/programyears',
        summary: "Create program years.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'programYears',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: ProgramYearDTO::class)
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
                description: 'An array of newly created program years.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'programYears',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: ProgramYearDTO::class)
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
        $data = $requestParser->extractPostDataFromRequest($request, $this->endpoint);
        // remove empty cohorts since we will be creating them later
        $cleanData = array_map(function ($obj) {
            if (empty($obj->cohort)) {
                unset($obj->cohort);
            }

            return $obj;
        }, $data);

        $class = $this->repository->getClass() . '[]';
        $entities = $this->serializer->deserialize(json_encode($cleanData), $class, 'json');

        foreach ($entities as $entity) {
            $this->validateAndAuthorizeEntity($entity, VoterPermissions::CREATE, $validator, $authorizationChecker);

            $this->repository->update($entity, false);
            $this->createCohort($entity);
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
        path: '/api/{version}/programyears/{id}',
        summary: 'Update or create a program year.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'programYear',
                        ref: new Model(type: ProgramYearDTO::class),
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
                description: 'The updated program year.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'programYear',
                            ref: new Model(type: ProgramYearDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created program year.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'programYear',
                            ref: new Model(type: ProgramYearDTO::class)
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
        /** @var ?ProgramYearInterface $entity */
        $entity = $this->repository->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = VoterPermissions::EDIT;
            $data = $requestParser->extractPutDataFromRequest($request, $this->endpoint);
            if (!$entity->isArchived() && $data->archived) {
                return $this->archiveProgramYear($entity, $builder, $authorizationChecker, $request);
            }
            if ($entity->isLocked() && !$data->locked) {
                return $this->unlockProgramYear($entity, $builder, $authorizationChecker, $request);
            }
            if (!$entity->isLocked() && $data->locked) {
                return $this->lockProgramYear($entity, $builder, $authorizationChecker, $request);
            }
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = VoterPermissions::CREATE;
        }

        /** @var ProgramYearInterface $entity */
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);

        $this->repository->update($entity, false, false);
        if (empty($entity->getCohort())) {
            $this->createCohort($entity);
        }

        $this->repository->flush();

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, $code, $request);
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
        return $this->handlePatch($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        path: '/api/{version}/programyears/{id}',
        summary: 'Delete a program year.',
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
        return $this->handleDelete($version, $id, $authorizationChecker);
    }

    #[Route(
        '/{id}/downloadobjectivesmapping',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/programyears/{id}/downloadobjectivesmapping',
        summary: 'Download the objective mapping as CSV.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A CSV file containing the objective mapping.'
            ),
        ]
    )]
    public function downloadCourseObjectivesReport(
        string $version,
        int $id
    ): Response {
        $dto = $this->repository->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $data = $this->programYearRepository->getProgramYearObjectiveToCourseObjectivesMapping($dto->id);

        array_walk($data, function (&$row): void {
            foreach (['program_year_objective', 'mapped_course_objective'] as $key) {
                if ($row[$key]) {
                    $row[$key] = strip_tags($row[$key]);
                }
            }
            $row['matriculation_year'] = $row['matriculation_year'] . ' - ' . ($row['matriculation_year'] + 1);
        });

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        return new Response(
            $serializer->serialize($data, 'csv'),
            Response::HTTP_OK,
            [
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'inline',
            ]
        );
    }

    /**
     * Creates a new cohort for a new program year.
     */
    protected function createCohort(ProgramYearInterface $programYear): void
    {
        $program = $programYear->getProgram();
        $graduationYear = $programYear->getStartYear() + $program->getDuration();

        /** @var CohortInterface $cohort */
        $cohort = $this->cohortRepository->create();
        $cohort->setTitle("Class of {$graduationYear}");
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);

        $this->cohortRepository->update($cohort, false, false);
    }

    protected function archiveProgramYear(
        ProgramYearInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request
    ): Response {
        if (!$authorizationChecker->isGranted(VoterPermissions::ARCHIVE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setArchived(true);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }

    protected function lockProgramYear(
        ProgramYearInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request
    ): Response {
        if (!$authorizationChecker->isGranted(VoterPermissions::LOCK, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setLocked(true);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }

    protected function unlockProgramYear(
        ProgramYearInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request
    ): Response {
        if (!$authorizationChecker->isGranted(VoterPermissions::UNLOCK, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setLocked(false);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }
}
