<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\CurriculumInventoryReport;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/{version<v1|v3>}/curriculuminventoryreports")
 */
class CurriculumInventoryReports extends ReadWriteController
{
    /**
     * @var CurriculumInventoryReportManager
     */
    protected $manager;
    protected CurriculumInventoryAcademicLevelManager $levelManager;
    protected CurriculumInventorySequenceManager $sequenceManager;

    public function __construct(
        CurriculumInventoryReportManager $manager,
        CurriculumInventoryAcademicLevelManager $levelManager,
        CurriculumInventorySequenceManager $sequenceManager
    ) {
        parent::__construct($manager, 'curriculuminventoryreports');
        $this->levelManager = $levelManager;
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * Handles POST which creates new data in the API
     * Along with the report create the Sequence and Levels that
     * are necessary for a Report to be at all valid
     * @Route("", methods={"POST"})
     */
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $class = CurriculumInventoryReport::class . '[]';

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
                $level = $this->levelManager->create();
                $level->setLevel($i);
                $level->setName('Year ' . $i); // @todo i18n 'Year'. [ST 2016/06/02]
                $entity->addAcademicLevel($level);
                $level->setReport($entity);
                $this->levelManager->update($level, false);
            }
            $sequence = $this->sequenceManager->create();
            $entity->setSequence($sequence);
            $sequence->setReport($entity);
            $this->sequenceManager->update($sequence, false);

            $this->manager->update($entity, false);
        }
        $this->manager->flush();

        foreach ($entities as $entity) {
            // generate token after the fact, since it needs to include the report id.
            $entity->generateToken();
            $this->manager->update($entity, false);
        }

        $this->manager->flush();

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $dtos, Response::HTTP_CREATED, $request);
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
        $dtos = $this->fetchDtosForEntities([$newReport]);

        return $builder->buildResponseForPostRequest(
            $this->endpoint,
            $dtos,
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
