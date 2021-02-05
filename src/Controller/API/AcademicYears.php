<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\AcademicYear;
use App\Repository\CourseRepository;
use App\Service\AcademicYearFactory;
use App\Service\ApiResponseBuilder;
use App\Service\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/{version<v3>}/academicyears")
 */
class AcademicYears
{
    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function getOne(
        string $version,
        string $id,
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
        $academicYear = $academicYearFactory->create((int) $id);
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

    /**
     * @Route("", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        CourseRepository $courseRepository,
        SerializerInterface $serializer,
        ApiResponseBuilder $builder,
        AcademicYearFactory $academicYearFactory
    ): Response {
        $years = array_map(function ($year) use ($academicYearFactory) {
            return $academicYearFactory->create($year);
        }, $courseRepository->getYears());

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
