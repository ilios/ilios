<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class MeshTreeController
 * @package Ilios\ApiBundle\Controller
 */
class MeshTreeController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     */
    public function deleteAction($version, $object, $id)
    {
        $this->fourTenAction();
    }

    /**
     * Generic action used by the router to send a 410 GONE
     * to anyone trying to POST, PUT or DELETE a MeSH Tree
     */
    public function fourTenAction()
    {
        throw new GoneHttpException('Creating, updating and deleting MeSH Trees is no longer supported.');
    }
}
