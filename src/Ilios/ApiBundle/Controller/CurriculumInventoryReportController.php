<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CurriculumInventoryExport
 * Reports require special handling.  They are decorated
 * for GET request, create levels when POSTed and
 * can be rolled over
 * @package Ilios\ApiBundle\Controller
 */
class CurriculumInventoryReportController extends NonDtoApiController
{
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractDataFromRequest($request, $object);
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        $levelManager = $this->get('ilioscore.curriculuminventoryacademiclevel.manager');
        $sequenceManager = $this->get('ilioscore.curriculuminventorysequence.manager');
        /** @var CurriculumInventoryReportInterface $entity */
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

            $manager->update($entity, false);

        }
        $manager->flush();

        foreach ($entities as $entity) {
            // generate token after the fact, since it needs to include the report id.
            $entity->generateToken();
            $manager->update($entity, false);
        }

        $manager->flushAndClear();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    protected function createResponse($responseKey, $value, $responseCode)
    {
        $factory = $this->get('ilioscore.curriculum_inventory_report_decorator.factory');
        if (is_array($value)) {
            $value = array_map(function (CurriculumInventoryReportInterface $report) use ($factory) {
                return $factory->create($report);
            }, $value);
        } else {
            $value = $factory->create($value);
        }


        return parent::createResponse($responseKey, $value, $responseCode);
    }

    /**
     * Rollover (clone) a given curriculum Inventory report, down to the sequence block level.
     */
    public function rolloverAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        /** @var CurriculumInventoryReportInterface $report */
        $report = $manager->findOneBy(['id' => $id]);

        if (! $report) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted(['create'], $report)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $name = $request->get('name');
        $description = $request->get('description');
        $year = $request->get('year');

        $service = $this->container->get('ilioscore.curriculum_inventory.rollover');
        $newReport = $service->rollover($report, $name, $description, $year);

        return $this->resultsToResponse([$newReport], $this->getPluralResponseKey($object), Response::HTTP_CREATED);
    }
}
