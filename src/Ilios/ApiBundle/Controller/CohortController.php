<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
