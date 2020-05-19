<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseManager;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AcademicYear
{
    public function get($version, $id, CourseManager $courseManager, ApiResponseBuilder $builder)
    {
        $years = $courseManager->getYears();

        if (!in_array($id, $years)) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $builder->buildPluralResponse('academicyears', [new \App\Classes\AcademicYear($id)], Response::HTTP_OK);
    }

    public function getAll(
        $version,
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