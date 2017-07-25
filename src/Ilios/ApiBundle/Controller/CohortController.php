<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class CohortController
 * Cohorts cannot be created, we need to reject any attempts to do so.
 */
class CohortController extends ApiController
{
    /**
     * Don't allow new cohorts to be created with a PUT request
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id'=> $id]);

        if (!$entity) {
            $this->throwCreatingCohortNotSupportedException();
        }

        return parent::putAction($version, $object, $id, $request);
    }

    /**
     * Generic action used by the router to send a 410 GONE
     * to anyone trying to POST or DELETE a cohort
     */
    public function fourTenAction()
    {
        $this->throwCreatingCohortNotSupportedException();
    }

    /**
     * @throws GoneHttpException
     */
    protected function throwCreatingCohortNotSupportedException()
    {
        throw new GoneHttpException('Explicitly creating and deleting cohorts is no longer supported.');
    }
}
