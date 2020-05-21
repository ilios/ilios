<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\CourseInterface;
use App\Entity\Manager\CourseManager;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\CourseRollover;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/{version<v1|v2>}/courses")
 */
class Courses extends ReadWriteController
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(CourseManager $manager, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($manager, 'courses');
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handle the special 'my' parameter for courses
     * @Route("/", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $my = $request->get('my');
        $parameters = ApiRequestParser::extractParameters($request);

        if (null !== $my) {
            /** @var SessionUserInterface $currentUser */
            $currentUser = $this->tokenStorage->getToken()->getUser();
            if ('v1' === $version) {
                $dtos = $this->manager->findCoursesByUserIdV1(
                    $currentUser->getId(),
                    $parameters['criteria'],
                    $parameters['orderBy'],
                    $parameters['limit'],
                    $parameters['offset']
                );
            } else {
                $dtos = $this->manager->findCoursesByUserId(
                    $currentUser->getId(),
                    $parameters['criteria'],
                    $parameters['orderBy'],
                    $parameters['limit'],
                    $parameters['offset']
                );
            }

            $filteredResults = array_filter($dtos, function ($object) use ($authorizationChecker) {
                return $authorizationChecker->isGranted(AbstractVoter::VIEW, $object);
            });

            //Re-index numerically index the array
            $values = array_values($filteredResults);

            return $builder->buildPluralResponse($this->endpoint, $values, Response::HTTP_OK);
        }

        return parent::getAll($version, $request, $authorizationChecker, $builder);
    }


    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     * @Route("/{id}<\d>", methods={"PUT"})
     */
    public function put(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        /** @var CourseInterface $entity */
        $entity = $this->manager->findOneBy(['id' => $id]);

        if ($entity) {
            $data = $requestParser->extractPutDataFromRequest($request, $this->endpoint);
            if (!$entity->isArchived() && $data->archived) {
                return $this->archiveCourse($entity, $builder, $authorizationChecker);
            }
            if ($entity->isLocked() && !$data->locked) {
                return $this->unlockCourse($entity, $builder, $authorizationChecker);
            }
            if (!$entity->isLocked() && $data->locked) {
                return $this->lockCourse($entity, $builder, $authorizationChecker);
            }
        }

        return parent::put($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }



    /**
     * Rollover a course by ID
     * @Route("/{id}/rollover", methods={"POST"})
     */
    public function rolloverAction(
        string $version,
        int $id,
        Request $request,
        CourseRollover $rolloverCourse,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $course = $this->manager->findOneBy(['id' => $id]);

        if (! $course) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::EDIT, $course)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $year = (int) $request->get('year');
        if (!$year) {
            throw new InvalidInputWithSafeUserMessageException("year is missing");
        }
        if ($year < 2000 || $year > 3000) {
            throw new InvalidInputWithSafeUserMessageException("year is invalid");
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

        $newCohortIds =  $request->get('newCohorts', []);

        $newCourse = $rolloverCourse->rolloverCourse($course->getId(), $year, $options, $newCohortIds);

        //pulling the DTO ensures we get all the new relationships
        $newCourseDTO = $this->manager->findDTOBy(['id' => $newCourse->getId()]);

        return $builder->buildPluralResponse($this->endpoint, [$newCourseDTO], Response::HTTP_CREATED);
    }


    protected function archiveCourse(
        CourseInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if (!$authorizationChecker->isGranted(AbstractVoter::ARCHIVE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setArchived(true);
        $this->manager->update($entity, true, false);

        return $builder->buildSingularResponse($this->endpoint, $entity, Response::HTTP_OK);
    }

    protected function lockCourse(
        CourseInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if (!$authorizationChecker->isGranted(AbstractVoter::LOCK, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setLocked(true);
        $this->manager->update($entity, true, false);

        return $builder->buildSingularResponse($this->endpoint, $entity, Response::HTTP_OK);
    }

    protected function unlockCourse(
        CourseInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if (!$authorizationChecker->isGranted(AbstractVoter::UNLOCK, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setLocked(false);
        $this->manager->update($entity, true, false);

        return $builder->buildSingularResponse($this->endpoint, $entity, Response::HTTP_OK);
    }
}
