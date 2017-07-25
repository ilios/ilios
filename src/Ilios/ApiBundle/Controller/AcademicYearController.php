<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Classes\AcademicYear;
use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AcademicYearController
 * Academic years cannot be created, we need to reject any attempts to do so.
 */
class AcademicYearController extends ApiController
{
    public function getAction($version, $object, $id)
    {
        /** @var CourseManager $courseManager */
        $courseManager = $this->getManager('courses');
        $years = $courseManager->getYears();

        if (!in_array($id, $years)) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $this->createResponse('academicYears', [new AcademicYear($id)], Response::HTTP_OK);
    }

    public function getAllAction($version, $object, Request $request)
    {
        /** @var CourseManager $courseManager */
        $courseManager = $this->getManager('courses');

        $years = array_map(function ($year) {
            return new AcademicYear($year);
        }, $courseManager->getYears());

        return $this->createResponse('academicYears', $years, Response::HTTP_OK);
    }

    public function fourOhFourAction()
    {
        throw new NotFoundHttpException(
            'Academic Years cannot be modified directly.  They must be attached to a course.'
        );
    }
}
