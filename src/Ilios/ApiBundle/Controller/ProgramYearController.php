<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProgramYearController
 * When program years are created cohorts
 * must be created at the same time
 * @package Ilios\ApiBundle\Controller
 */
class ProgramYearController extends ApiController
{
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractDataFromRequest($request, $object);
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
            $this->createCohort($entity);
        }
        $manager->flushAndClear();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        /** @var ProgramYearInterface $entity */
        $entity = $manager->findOneBy(['id'=> $id]);
        $data = $this->extractDataFromRequest($request, $object, $singleItem = true, $returnData = true);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
            $authChecker = $this->get('security.authorization_checker');
            if ($entity->isLocked() && !$data->locked) {
                //check if the programYear can be unlocked and unlock it
                if ($authChecker->isGranted('unlock', $entity)) {
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
        $serializer->deserialize($json, get_class($entity), 'json', array('object_to_populate' => $entity));
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
        $cohortManager = $this->container->get('ilioscore.cohort.manager');

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
