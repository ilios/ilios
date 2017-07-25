<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Service\CourseRollover;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CoursesController
 * We have to handle a special 'my' parameter on courses
 * so it needs its own controller
 */
class CoursesController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function getAllAction($version, $object, Request $request)
    {
        $my = $request->get('my');
        $parameters = $this->extractParameters($request);

        /** @var CourseManager $manager */
        $manager = $this->getManager($object);

        if (null !== $my) {
            /** @var SessionUserInterface $currentUser */
            $currentUser = $this->tokenStorage->getToken()->getUser();
            $result = $manager->findCoursesByUserId(
                $currentUser->getId(),
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
        }

        return parent::getAllAction($version, $object, $request);
    }

    /**
     * Allow courses to be unlocked if necessary
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        /** @var CourseInterface $entity */
        $entity = $manager->findOneBy(['id'=> $id]);
        $data = $this->extractPutDataFromRequest($request, $object);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
            if ($entity->isLocked() && !$data->locked) {
                //check if the course can be unlocked and unlock it
                if ($this->authorizationChecker->isGranted('unlock', $entity)) {
                    $entity->setLocked(false);
                }
                $data->locked = $entity->isLocked();
            }
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }
        $json = json_encode($data);
        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, true, false);

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    /**
     * Rollover a course by ID
     *
     * @param string $version
     * @param string $object
     * @param int $id
     * @param Request $request
     * @param CourseRollover $rolloverCourse
     *
     * @return Response
     */
    public function rolloverAction($version, $object, $id, Request $request, CourseRollover $rolloverCourse)
    {
        $manager = $this->getManager($object);
        $course = $manager->findOneBy(['id' => $id]);

        if (! $course) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $this->authorizationChecker->isGranted(['edit'], $course)) {
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

        $newCourse = $rolloverCourse->rolloverCourse($course->getId(), $year, $options);

        //pulling the DTO ensures we get all the new relationships
        $newCourseDTO = $manager->findDTOBy(['id' => $newCourse->getId()]);

        return $this->createResponse($this->getPluralResponseKey($object), [$newCourseDTO], Response::HTTP_CREATED);
    }

    /**
     * Along with extracting the parameters this converts
     * the datetime ones into DateTime objects from strings
     * @inheritdoc
     */
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
