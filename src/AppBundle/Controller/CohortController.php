<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class CohortController
 * @package AppBundle\Controller
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
            $this->fourTenAction();
        }

        return parent::putAction($version, $object, $id, $request);
    }

    /**
     * @inheritdoc
     * @deprecated
     */
    public function postAction($version, $object, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     * @deprecated
     */
    public function deleteAction($version, $object, $id)
    {
        $this->fourTenAction();
    }

    /**
     * Generic action used by the router to send a 410 GONE
     * to anyone trying to POST or DELETE a cohort
     * @throws GoneHttpException
     */
    public function fourTenAction()
    {
        throw new GoneHttpException('Explicitly creating and deleting cohorts is no longer supported.');
    }
}
