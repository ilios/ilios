<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class PermissionController
 * @package Ilios\ApiBundle\Controller
 */
class PermissionController extends ApiController
{
    /**
     * @inheritdoc
     * @deprecated
     */
    public function postAction($version, $object, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritDoc
     * @deprecated
     */
    public function getAction($version, $object, $id)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     * @deprecated
     */
    public function getAllAction($version, $object, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     * @deprecated
     */
    public function putAction($version, $object, $id, Request $request)
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
     * to anyone trying to interact with Permissions.
     * @throws GoneHttpException
     */
    public function fourTenAction()
    {
        throw new GoneHttpException(
            'Accessing Permissions is no longer supported.'
        );
    }
}
