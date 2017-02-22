<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CoursesController
 * We have to handle a special 'my' parameter on courses
 * so it needs its own controller
 * @package Ilios\ApiBundle\Controller
 */
class CoursesController extends ApiController
{
    public function getAllAction($version, $object, Request $request)
    {
        $my = $request->get('my');
        $parameters = $this->extractParameters($request);

        /** @var CourseManager $manager */
        $manager = $this->getManager($object);

        if (null !== $my) {
            $currentUser = $this->get('security.token_storage')->getToken()->getUser();
            $result = $manager->findCoursesByUser(
                $currentUser,
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            return $this->resultsToResponse($result, $object, Response::HTTP_OK);
        }

        return parent::getAllAction($version, $object, $request);
    }

    public function rolloverAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        $course = $manager->findOneBy(['id' => $id]);

        if (! $course) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');

        if (! $authChecker->isGranted(['edit'], $course)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $year = $request->get('year');
        if (!$year) {
            throw new InvalidInputWithSafeUserMessageException("year is missing");
        }
        $options = [];
        $options['new-start-date'] = $request->get('newStartDate');
        $options['skip-offerings'] = $request->get('skipOfferings');
        $options['new-course-title'] = $request->get('newCourseTitle');

        $options = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $options);

        $rolloverCourse = $this->container->get('ilioscore.courserollover');
        $newCourse = $rolloverCourse->rolloverCourse($course->getId(), $year, $options);

        //pulling the DTO ensures we get all the new relationships
        $newCourseDTO = $manager->findDTOBy(['id' => $newCourse->getId()]);

        return $this->createResponse($object, [$newCourseDTO], Response::HTTP_CREATED);
    }

    protected function extractParameters(Request $request)
    {
        $parameters = parent::extractParameters($request);
        $dateTimes = ['startDate', 'endDate'];
        foreach ($dateTimes as $key) {
            if (array_key_exists($key, $parameters['criteria'])) {
                $parameters['criteria'][$key] = new \DateTime($parameters['criteria'][$key]);
            }
        }


        return $parameters;
    }
}
