<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\LearningMaterialUserRoleHandler;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * LearningMaterialUserRole controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("LearningMaterialUserRole")
 */
class LearningMaterialUserRoleController extends FOSRestController
{
    
    /**
     * Get a LearningMaterialUserRole
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $answer['learningMaterialUserRole'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all LearningMaterialUserRole.
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

        $answer['learningMaterialUserRole'] =
            $this->getLearningMaterialUserRoleHandler()->findLearningMaterialUserRolesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['learningMaterialUserRole']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a LearningMaterialUserRole.
     *
     * @View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $new  =  $this->getLearningMaterialUserRoleHandler()->post($request->request->all());
            $answer['learningMaterialUserRole'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearningMaterialUserRole.
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            if ($learningMaterialUserRole = $this->getLearningMaterialUserRoleHandler()->findLearningMaterialUserRoleBy(['id'=> $id])) {
                $answer['learningMaterialUserRole']= $this->getLearningMaterialUserRoleHandler()->put($learningMaterialUserRole, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['learningMaterialUserRole'] = $this->getLearningMaterialUserRoleHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a LearningMaterialUserRole.
     *
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['learningMaterialUserRole'] = $this->getLearningMaterialUserRoleHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a LearningMaterialUserRole.
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal LearningMaterialUserRoleInterface $learningMaterialUserRole
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $learningMaterialUserRole = $this->getOr404($id);
        try {
            $this->getLearningMaterialUserRoleHandler()->deleteLearningMaterialUserRole($learningMaterialUserRole);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return LearningMaterialUserRoleInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getLearningMaterialUserRoleHandler()->findLearningMaterialUserRoleBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return LearningMaterialUserRoleHandler
     */
    protected function getLearningMaterialUserRoleHandler()
    {
        return $this->container->get('ilioscore.learningmaterialuserrole.handler');
    }
}
