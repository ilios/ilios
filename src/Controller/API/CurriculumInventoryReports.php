<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\Manager\CurriculumInventoryAcademicLevelManager;
use App\Entity\Manager\CurriculumInventoryReportManager;
use App\Entity\Manager\CurriculumInventorySequenceManager;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\CurriculumInventory\ReportRollover;
use App\Service\CurriculumInventory\VerificationPreviewBuilder;
use App\Service\CurriculumInventoryReportDecoratorFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use RuntimeException;

/**
 * reports need to be decorated with their absolute file path
 * before they can be sent, otherwise the method bodies are copied from
 * the top level API Read and ReadWrite controllers
 *
 * @Route("/api/{version<v1|v2>}/curriculuminventoryreports")
 */
class CurriculumInventoryReports
{
    /**
     * @var CurriculumInventoryReportManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var CurriculumInventoryReportDecoratorFactory
     */
    protected $factory;

    public function __construct(
        CurriculumInventoryReportManager $manager,
        CurriculumInventoryReportDecoratorFactory $factory
    ) {
        $this->manager = $manager;
        $this->endpoint = 'curriculuminventoryreports';
        $this->factory = $factory;
    }

    /**
     * Handles GET request for a single entity
     * @Route("/{id}", methods={"GET"})
     */
    public function getOne(
        string $version,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $dto = $this->manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $values =  [];

        if ($authorizationChecker->isGranted(AbstractVoter::VIEW, $dto)) {
            $values = [$this->factory->create($dto)];
        }

        return $builder->buildResponseForGetOneRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles GET request for multiple entities
     * @Route("/", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $parameters = ApiRequestParser::extractParameters($request);
        $dtos = $this->manager->findDTOsBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );

        $filteredResults = array_filter($dtos, function ($object) use ($authorizationChecker) {
            return $authorizationChecker->isGranted(AbstractVoter::VIEW, $object);
        });

        $factory = $this->factory;
        $values = array_map(function ($report) use ($factory) {
            return $factory->create($report);
        }, $filteredResults);

        //Re-index numerically index the array
        $values = array_values($values);

        return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles POST which creates new data in the API
     * Along with the report create the Sequence and Levels that
     * are necessary for a Report to be at all valid
     * @Route("/", methods={"POST"})
     */
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        CurriculumInventoryAcademicLevelManager $levelManager,
        CurriculumInventorySequenceManager $sequenceManager
    ): Response {
        $class = $this->manager->getClass() . '[]';

        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $this->endpoint);

        foreach ($entities as $entity) {
            $errors = $validator->validate($entity);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
            }
            if (! $authorizationChecker->isGranted(AbstractVoter::CREATE, $entity)) {
                throw new AccessDeniedException('Unauthorized access!');
            }
        }

        foreach ($entities as $entity) {
            // create academic years and sequence while at it.
            for ($i = 1, $n = 10; $i <= $n; $i++) {
                $level = $levelManager->create();
                $level->setLevel($i);
                $level->setName('Year ' . $i); // @todo i18n 'Year'. [ST 2016/06/02]
                $entity->addAcademicLevel($level);
                $level->setReport($entity);
                $levelManager->update($level, false);
            }
            $sequence = $sequenceManager->create();
            $entity->setSequence($sequence);
            $sequence->setReport($entity);
            $sequenceManager->update($sequence, false);

            $this->manager->update($entity, false);
        }
        $this->manager->flush();

        foreach ($entities as $entity) {
            // generate token after the fact, since it needs to include the report id.
            $entity->generateToken();
            $this->manager->update($entity, false);
        }

        $this->manager->flush();

        $factory = $this->factory;
        $values = array_map(function ($report) use ($factory) {
            return $factory->create($report);
        }, $entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $values, Response::HTTP_CREATED, $request);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     * @Route("/{id}", methods={"PUT"})
     */
    public function put(
        string $version,
        int $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $entity = $this->manager->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->manager->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }

        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->manager->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $this->factory->create($entity), $code, $request);
    }


    /**
     * Handles DELETE requests to remove an element from the API
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(
        string $version,
        int $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->manager->findOneBy(['id' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::DELETE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        try {
            $this->manager->delete($entity);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to delete entity: " . $exception->getMessage());
        }
    }

    /**
     * Rollover (clone) a given curriculum Inventory report, down to the sequence block level.
     * @Route("/{id}/rollover", methods={"POST"})
     */
    public function rollover(
        string $version,
        int $id,
        Request $request,
        ReportRollover $rollover,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        /** @var CurriculumInventoryReportInterface $report */
        $report = $this->manager->findOneBy(['id' => $id]);

        if (! $report) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::ROLLOVER, $report)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $name = $request->get('name');
        $description = $request->get('description');

        $year = $request->get('year');
        if ($year) {
            $year = (int) $year;
            if ($year < 2000 || $year > 3000) {
                throw new InvalidInputWithSafeUserMessageException("year is invalid");
            }
        }

        $newReport = $rollover->rollover($report, $name, $description, $year);

        return $builder->buildResponseForPostRequest(
            $this->endpoint,
            [$this->factory->create($newReport)],
            Response::HTTP_CREATED,
            $request
        );
    }

    /**
     * Build and send the verification preview tables for CI
     * @Route("/{id}/verificationpreview", methods={"GET"})
     */
    public function verificationPreview(
        string $version,
        int $id,
        AuthorizationCheckerInterface $authorizationChecker,
        VerificationPreviewBuilder $previewBuilder,
        SerializerInterface $serializer
    ): Response {
        /* @var CurriculumInventoryReportInterface $report */
        $report = $this->manager->findOneBy(['id' => $id]);

        if (! $report) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::VIEW, $report)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $tables = $previewBuilder->build($report);

        return new Response(
            $serializer->serialize(
                [ 'preview' => $tables],
                'json'
            ),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
