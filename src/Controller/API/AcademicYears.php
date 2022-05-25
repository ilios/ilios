<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CourseRepository;
use App\Service\AcademicYearFactory;
use App\Service\ApiResponseBuilder;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[OA\Tag(name:'Academic years')]
#[Route('/api/{version<v3>}/academicyears')]
class AcademicYears
{
    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/academicyears/{id}',
        summary: 'Fetch a single academic years.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path')
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'A single academic year.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'academicYears',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property("id", type: "string"),
                            new OA\Property("title", type: "string")
                        ],
                        type: "object"
                    )
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: '404', description: 'Not found.')]
    public function getOne(
        string $version,
        int $id,
        Request $request,
        CourseRepository $courseRepository,
        SerializerInterface $serializer,
        ApiResponseBuilder $builder,
        AcademicYearFactory $academicYearFactory
    ): Response {
        $years = $courseRepository->getYears();

        if (!in_array($id, $years)) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $contentTypes = $request->getAcceptableContentTypes();
        $academicYear = $academicYearFactory->create($id);
        if (in_array('application/vnd.api+json', $contentTypes)) {
            $json = $serializer->serialize([ $academicYear ], 'json-api', [
                'sideLoadFields' =>
                    $builder->extractJsonApiSideLoadFields(
                        $request->query->has('include') ? $request->query->all()['include'] : null
                    ),
                'singleItem' => true
            ]);
            return new Response(
                $json,
                Response::HTTP_OK,
                ['Content-type' => 'application/vnd.api+json']
            );
        } else {
            return new Response(
                $serializer->serialize(
                    [ 'academicYears' => [$academicYear]],
                    'json'
                ),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
        }
    }
    #[Route(
        methods: ['GET']
    )]
    #[OA\Get(
        path: "/api/{version}/academicyears",
        summary: "Fetch all academic years.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'An array of academic years.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'academicYears',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property("id", type: "string"),
                            new OA\Property("title", type: "string")
                        ],
                        type: "object"
                    )
                )
            ],
            type: 'object'
        )
    )]
    public function getAll(
        string $version,
        Request $request,
        CourseRepository $courseRepository,
        SerializerInterface $serializer,
        ApiResponseBuilder $builder,
        AcademicYearFactory $academicYearFactory
    ): Response {
        $years = array_map(fn($year) => $academicYearFactory->create($year), $courseRepository->getYears());
        $contentTypes = $request->getAcceptableContentTypes();
        if (in_array('application/vnd.api+json', $contentTypes)) {
            $json = $serializer->serialize($years, 'json-api', [
                'sideLoadFields' =>
                    $builder->extractJsonApiSideLoadFields(
                        $request->query->has('include') ? $request->query->all()['include'] : null
                    ),
                'singleItem' => false
            ]);
            return new Response(
                $json,
                Response::HTTP_OK,
                ['Content-type' => 'application/vnd.api+json']
            );
        } else {
            return new Response(
                $serializer->serialize(
                    [ 'academicYears' => $years],
                    'json'
                ),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
        }
    }
}
