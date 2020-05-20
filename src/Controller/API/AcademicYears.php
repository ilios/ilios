<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseManager;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/academicyears")
 */
class AcademicYears
{
    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function get(string $version, string $id, CourseManager $courseManager, ApiResponseBuilder $builder)
    {
        $years = $courseManager->getYears();

        if (!in_array($id, $years)) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $builder->buildPluralResponse('academicyears', [new \App\Classes\AcademicYear($id)], Response::HTTP_OK);
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        CourseManager $courseManager,
        ApiResponseBuilder $builder
    ) {
        $years = array_map(function ($year) {
            return new \App\Classes\AcademicYear($year);
        }, $courseManager->getYears());

        return $builder->buildPluralResponse('academicyears', $years, Response::HTTP_OK);
    }
}
