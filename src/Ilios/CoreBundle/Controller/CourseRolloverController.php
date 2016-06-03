<?php

namespace Ilios\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CourseRolloverController extends Controller
{

    public function indexAction($courseId, $newCourseAcademicYear, $newCourseStartDate = null)
    {
        $service = $this->getContainer()->get('ilioscore.courserollover');
        $service->rolloverCourse($courseId, $newCourseAcademicYear, $newCourseStartDate);

    }

}
