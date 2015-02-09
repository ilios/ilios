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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\CurriculumInventorySequenceBlockSessionHandler;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * CurriculumInventorySequenceBlockSession controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CurriculumInventorySequenceBlockSession")
 */
class CurriculumInventorySequenceBlockSessionController extends FOSRestController
{
    
    /**
     * Get a CurriculumInventorySequenceBlockSession
     *
     * @ApiDoc(
     *   description = "Get a CurriculumInventorySequenceBlockSession.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="bigint",
     *        "requirement"="",
     *        "description"="CurriculumInventorySequenceBlockSession identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes={
     *     200 = "CurriculumInventorySequenceBlockSession.",
     *     404 = "Not Found."
     *   }
     * )
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
        $answer['curriculumInventorySequenceBlockSession'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CurriculumInventorySequenceBlockSession.",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventorySequenceBlockSession",
     *     204 = "No content. Nothing to list."
     *   }
     * )
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

        $criteria = array_map(function ($item) {
            $item = $item == 'null'?null:$item;
            $item = $item == 'false'?false:$item;
            $item = $item == 'true'?true:$item;
            return $item;
        }, $criteria);

        $result = $this->getCurriculumInventorySequenceBlockSessionHandler()
            ->findCurriculumInventorySequenceBlockSessionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['curriculumInventorySequenceBlockSessions'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CurriculumInventorySequenceBlockSession.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventorySequenceBlockSessionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes={
     *     201 = "Created CurriculumInventorySequenceBlockSession.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
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
            $new  =  $this->getCurriculumInventorySequenceBlockSessionHandler()->post($this->getPostData($request));
            $answer['curriculumInventorySequenceBlockSession'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CurriculumInventorySequenceBlockSession entity.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventorySequenceBlockSessionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes={
     *     200 = "Updated CurriculumInventorySequenceBlockSession.",
     *     201 = "Created CurriculumInventorySequenceBlockSession.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
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
            $curriculumInventorySequenceBlockSession = $this->getCurriculumInventorySequenceBlockSessionHandler()
                ->findCurriculumInventorySequenceBlockSessionBy(['id'=> $id]);
            if ($curriculumInventorySequenceBlockSession) {
                $answer['curriculumInventorySequenceBlockSession'] =
                    $this->getCurriculumInventorySequenceBlockSessionHandler()->put(
                        $curriculumInventorySequenceBlockSession,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['curriculumInventorySequenceBlockSession'] =
                    $this->getCurriculumInventorySequenceBlockSessionHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CurriculumInventorySequenceBlockSession.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventorySequenceBlockSessionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="bigint",
     *         "requirement"="",
     *         "description"="CurriculumInventorySequenceBlockSession identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated CurriculumInventorySequenceBlockSession.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
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
        $answer['curriculumInventorySequenceBlockSession'] =
            $this->getCurriculumInventorySequenceBlockSessionHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   description = "Delete a CurriculumInventorySequenceBlockSession entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "bigint",
     *         "requirement" = "",
     *         "description" = "CurriculumInventorySequenceBlockSession identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventorySequenceBlockSession.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $curriculumInventorySequenceBlockSession = $this->getOr404($id);
        try {
            $this->getCurriculumInventorySequenceBlockSessionHandler()
                ->deleteCurriculumInventorySequenceBlockSession($curriculumInventorySequenceBlockSession);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventorySequenceBlockSessionInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getCurriculumInventorySequenceBlockSessionHandler()
            ->findCurriculumInventorySequenceBlockSessionBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }
   /**
    * Parse the request for the form data
    *
    * @param Request $request
    * @return array
     */
    protected function getPostData(Request $request)
    {
        return $request->request->get('curriculumInventorySequenceBlockSession', array());
    }
    /**
     * @return CurriculumInventorySequenceBlockSessionHandler
     */
    protected function getCurriculumInventorySequenceBlockSessionHandler()
    {
        return $this->container->get('ilioscore.curriculuminventorysequenceblocksession.handler');
    }
}
