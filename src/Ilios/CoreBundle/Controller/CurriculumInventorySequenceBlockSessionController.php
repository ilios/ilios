<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\CurriculumInventorySequenceBlockSessionHandler;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventorySequenceBlockSessions")
 */
class CurriculumInventorySequenceBlockSessionController extends FOSRestController
{
    /**
     * Get a CurriculumInventorySequenceBlockSession
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlockSession",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['curriculumInventorySequenceBlockSession'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlockSession",
     *   description = "Get all CurriculumInventorySequenceBlockSession.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventorySequenceBlockSession",
     *     204 = "No content. Nothing to list."
     *   }
     * )
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
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

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
     *   section = "CurriculumInventorySequenceBlockSession",
     *   description = "Create a CurriculumInventorySequenceBlockSession.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceBlockSessionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes={
     *     201 = "Created CurriculumInventorySequenceBlockSession.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $curriculuminventorysequenceblocksession = $this->getCurriculumInventorySequenceBlockSessionHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_curriculuminventorysequenceblocksessions',
                    ['id' => $curriculuminventorysequenceblocksession->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventorySequenceBlockSession.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlockSession",
     *   description = "Update a CurriculumInventorySequenceBlockSession entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceBlockSessionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession",
     *   statusCodes={
     *     200 = "Updated CurriculumInventorySequenceBlockSession.",
     *     201 = "Created CurriculumInventorySequenceBlockSession.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $curriculumInventorySequenceBlockSession = $this->getCurriculumInventorySequenceBlockSessionHandler()
                ->findCurriculumInventorySequenceBlockSessionBy(['id'=> $id]);
            if ($curriculumInventorySequenceBlockSession) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventorySequenceBlockSession = $this->getCurriculumInventorySequenceBlockSessionHandler()->createCurriculumInventorySequenceBlockSession();
                $code = Codes::HTTP_CREATED;
            }

            $answer['curriculumInventorySequenceBlockSession'] =
                $this->getCurriculumInventorySequenceBlockSessionHandler()->put(
                    $curriculumInventorySequenceBlockSession,
                    $this->getPostData($request)
                );
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
     *   section = "CurriculumInventorySequenceBlockSession",
     *   description = "Partial Update to a CurriculumInventorySequenceBlockSession.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceBlockSessionType",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
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
     *   section = "CurriculumInventorySequenceBlockSession",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventorySequenceBlockSession = $this->getOr404($id);

        try {
            $this->getCurriculumInventorySequenceBlockSessionHandler()->deleteCurriculumInventorySequenceBlockSession($curriculumInventorySequenceBlockSession);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     */
    protected function getOr404($id)
    {
        $curriculumInventorySequenceBlockSession = $this->getCurriculumInventorySequenceBlockSessionHandler()
            ->findCurriculumInventorySequenceBlockSessionBy(['id' => $id]);
        if (!$curriculumInventorySequenceBlockSession) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventorySequenceBlockSession;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('curriculumInventorySequenceBlockSession');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CurriculumInventorySequenceBlockSessionHandler
     */
    protected function getCurriculumInventorySequenceBlockSessionHandler()
    {
        return $this->container->get('ilioscore.curriculuminventorysequenceblocksession.handler');
    }
}
