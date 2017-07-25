<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProgramYearController
 * When program years are created cohorts
 * must be created at the same time
 */
class ProgramYearController extends ApiController
{
    /**
     * Create cohort to match the new program year
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
            $this->createCohort($entity);
        }
        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * Allow program years to be unlocked if necessary
     * and add a cohort if one does not already exist
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        /** @var ProgramYearInterface $entity */
        $entity = $manager->findOneBy(['id'=> $id]);
        $data = $this->extractPutDataFromRequest($request, $object);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
            if ($entity->isLocked() && !$data->locked) {
                //check if the programYear can be unlocked and unlock it
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

        if (empty($entity->getCohort())) {
            $this->createCohort($entity);
        }

        $manager->update($entity, true, false);

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    /**
     * Creates a new cohort for a new program year.
     * @param ProgramYearInterface $programYear
     */
    protected function createCohort(ProgramYearInterface $programYear)
    {
        $cohortManager = $this->getManager('cohorts');

        $program = $programYear->getProgram();
        $graduationYear = $programYear->getStartYear() + $program->getDuration();

        /* @var CohortInterface $cohort */
        $cohort = $cohortManager->create();
        $cohort->setTitle("Class of ${graduationYear}");
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);

        $cohortManager->update($cohort, false, false);
    }
}
