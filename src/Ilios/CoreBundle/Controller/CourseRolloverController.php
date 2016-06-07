<?php

namespace Ilios\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CourseRolloverController extends Controller
{
    /**
     * @param $args
     * @param $options
     */
    public function indexAction($args, $options) {

        $service = $this->getContainer()->get('ilioscore.courserollover');
        $service->rolloverCourse($args, $options);

    }
}
