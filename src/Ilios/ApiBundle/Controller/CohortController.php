<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class CohortController
 * Cohorts cannot be created, we need to reject any attempts to do so.
 * @package Ilios\ApiBundle\Controller
 */
class CohortController extends ApiController
{
    public function postAction($version, $object, Request $request)
    {
        $this->throwCreatingCohortNotSupportedException();
    }

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
     * @throws GoneHttpException
     */
    protected function throwCreatingCohortNotSupportedException()
    {
        throw new GoneHttpException('Explicitly creating cohorts is no longer supported.');
    }
}
