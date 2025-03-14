<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\ProgramInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\CurriculumInventoryAcademicLevelRepository;
use App\Repository\CurriculumInventoryReportRepository;
use App\Repository\CurriculumInventorySequenceRepository;
use App\Repository\ProgramRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\CurriculumInventory\ReportRollover;
use App\Service\CurriculumInventory\VerificationPreviewBuilder;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Curriculum inventory reports')]
#[Route('/api/{version<v3>}/curriculuminventoryreports')]
class CurriculumInventoryReports extends AbstractApiController
{
    public function __construct(
        CurriculumInventoryReportRepository $repository,
        protected CurriculumInventoryAcademicLevelRepository $levelManager,
        protected CurriculumInventorySequenceRepository $sequenceManager,
        protected ProgramRepository $programRepository,
    ) {
        parent::__construct($repository, 'curriculuminventoryreports');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/curriculuminventoryreports/{id}',
        summary: 'Fetch a single curriculum inventory report.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single curriculum inventory reports.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventoryReports',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventoryReportDTO::class)
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
        path: "/api/{version}/curriculuminventoryreports",
        summary: "Fetch all curriculum inventory reports.",
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
                description: 'An array of curriculum inventory reports.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventoryReports',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventoryReportDTO::class)
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

    /**
     * Handles POST which creates new data in the API
     * Along with the report create the Sequence and Levels that
     * are necessary for a Report to be at all valid
     */
    #[Route(methods: ['POST'])]
    #[OA\Post(
        path: '/api/{version}/curriculuminventoryreports',
        summary: "Create curriculum inventory reports.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'curriculumInventoryReports',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: CurriculumInventoryReportDTO::class)
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
                description: 'An array of newly created curriculum inventory reports.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventoryReports',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventoryReportDTO::class)
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
        $class = CurriculumInventoryReport::class . '[]';
        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $this->endpoint);
        $this->validateAndAuthorizeEntities($entities, VoterPermissions::CREATE, $validator, $authorizationChecker);

        foreach ($entities as $entity) {
            // create academic years and sequence while at it.
            for ($i = 1, $n = 10; $i <= $n; $i++) {
                $level = $this->levelManager->create();
                $level->setLevel($i);
                $level->setName('Year ' . $i); // @todo i18n 'Year'. [ST 2016/06/02]
                $entity->addAcademicLevel($level);
                $level->setReport($entity);
                $this->levelManager->update($level, false);
            }
            $sequence = $this->sequenceManager->create();
            $entity->setSequence($sequence);
            $sequence->setReport($entity);
            $this->sequenceManager->update($sequence, false);

            $this->repository->update($entity, false);
        }
        $this->repository->flush();

        foreach ($entities as $entity) {
            // generate token after the fact, since it needs to include the report id.
            $entity->generateToken();
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
        path: '/api/{version}/curriculuminventoryreports/{id}',
        summary: 'Update or create a curriculum inventory report.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'curriculumInventoryReport',
                        ref: new Model(type: CurriculumInventoryReportDTO::class),
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
                description: 'The updated curriculum inventory report.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventoryReport',
                            ref: new Model(type: CurriculumInventoryReportDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created curriculum inventory report.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventoryReport',
                            ref: new Model(type: CurriculumInventoryReportDTO::class)
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
        return $this->handlePut($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
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
        path: '/api/{version}/curriculuminventoryreports/{id}',
        summary: 'Delete a curriculum inventory report.',
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

    /**
     * Rollover a report by ID.
     */
    #[Route(
        '/{id}/rollover',
        methods: ['POST']
    )]
    #[OA\Post(
        path: '/api/{version}/{id}/rollover',
        summary: 'Rollover a report by ID.',
        requestBody: new OA\RequestBody(
            required: true,
            content: [new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            'name',
                            description: 'Name',
                            type: 'string'
                        ),
                        new OA\Property(
                            'description',
                            description: 'Description',
                            type: 'string'
                        ),
                        new OA\Property(
                            'year',
                            description: 'Year',
                            type: 'string'
                        ),
                        new OA\Property(
                            'program',
                            description: 'Program ID',
                            type: 'string',
                        ),
                    ],
                    type: 'object'
                )
            )]
        ),
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '201',
                description: 'An array containing the rolled-over report.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'curriculumInventoryReports',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: CurriculumInventoryReportDTO::class)
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
    public function rollover(
        string $version,
        int $id,
        Request $request,
        ReportRollover $rollover,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $report = $this->repository->findOneBy(['id' => $id]);

        if (! $report) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(VoterPermissions::ROLLOVER, $report)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $name = $request->get('name');
        $description = $request->get('description');

        $year = $request->get('year');
        if ($year) {
            $year = (int) $year;
            if ($year < 2000 || $year > 3000) {
                throw new InvalidInputWithSafeUserMessageException("year is invalid");
            }
        }

        $program = $report->getProgram();
        // optional program override
        $programId = (int) $request->get('program');
        if ($programId) {
            $program = $this->programRepository->findOneById($programId);
            if (! $program) {
                throw new InvalidInputWithSafeUserMessageException("no program with id = {$programId} exists.");
            }
        }

        $newReport = $rollover->rollover($report, $program, $name, $description, $year);
        $dtos = $this->fetchDtosForEntities([$newReport]);

        return $builder->buildResponseForPostRequest(
            $this->endpoint,
            $dtos,
            Response::HTTP_CREATED,
            $request
        );
    }

    /**
     * Build and send the verification preview tables for CI
     * @throws Exception
     */
    #[Route(
        '/{id}/verificationpreview',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/curriculuminventoryreports/{id}/verificationpreview',
        summary: 'Fetch verification preview data for a given report.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'The verification preview data.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'preview',
                            type: 'object',
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '403', description: 'Access Denied.'),
        ]
    )]
    public function verificationPreview(
        string $version,
        int $id,
        AuthorizationCheckerInterface $authorizationChecker,
        VerificationPreviewBuilder $previewBuilder
    ): Response {
        /** @var ?CurriculumInventoryReportInterface $report */
        $report = $this->repository->findOneBy(['id' => $id]);

        if (! $report) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(VoterPermissions::VIEW, $report)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $tables = $previewBuilder->build($report);

        return new Response(
            json_encode(['preview' => $tables]),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
