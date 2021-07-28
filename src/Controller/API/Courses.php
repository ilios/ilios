<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\CourseInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\CourseRepository;
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
 * @Route("/api/{version<v3>}/courses")
 */
class Courses extends ReadWriteController
{
    public function __construct(CourseRepository $repository, protected TokenStorageInterface $tokenStorage)
    {
        parent::__construct($repository, 'courses');
    }

    /**
     * Handle the special 'my' parameter for courses
     * @Route("", methods={"GET"})
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
            $dtos = $this->repository->findByUserId(
                $currentUser->getId(),
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            $filteredResults = array_filter(
                $dtos,
                fn($object) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $object)
            );

            //Re-index numerically index the array
            $values = array_values($filteredResults);

            return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
        }

        return parent::getAll($version, $request, $authorizationChecker, $builder);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     * @Route("/{id}", methods={"PUT"})
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
        $entity = $this->repository->findOneBy(['id' => $id]);

        if ($entity) {
            $data = $requestParser->extractPutDataFromRequest($request, $this->endpoint);
            if (!$entity->isArchived() && $data->archived) {
                return $this->archiveCourse($entity, $builder, $authorizationChecker, $request);
            }
            if ($entity->isLocked() && !$data->locked) {
                return $this->unlockCourse($entity, $builder, $authorizationChecker, $request);
            }
            if (!$entity->isLocked() && $data->locked) {
                return $this->lockCourse($entity, $builder, $authorizationChecker, $request);
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
        $course = $this->repository->findOneBy(['id' => $id]);

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
        $newCourseDTO = $this->repository->findDTOBy(['id' => $newCourse->getId()]);

        return $builder->buildResponseForPostRequest(
            $this->endpoint,
            [$newCourseDTO],
            Response::HTTP_CREATED,
            $request
        );
    }

    protected function archiveCourse(
        CourseInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request
    ): Response {
        if (!$authorizationChecker->isGranted(AbstractVoter::ARCHIVE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setArchived(true);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }

    protected function lockCourse(
        CourseInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request
    ): Response {
        if (!$authorizationChecker->isGranted(AbstractVoter::LOCK, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setLocked(true);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }

    protected function unlockCourse(
        CourseInterface $entity,
        ApiResponseBuilder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request
    ): Response {
        if (!$authorizationChecker->isGranted(AbstractVoter::UNLOCK, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        $entity->setLocked(false);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }
}
