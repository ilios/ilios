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
use Ilios\CoreBundle\Handler\ProgramYearHandler;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Class ProgramYearController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("ProgramYears")
 */
class ProgramYearController extends FOSRestController
{
    /**
     * Get a ProgramYear
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Get a ProgramYear.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="ProgramYear identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     200 = "ProgramYear.",
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
        $answer['programYear'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Get all ProgramYear.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes = {
     *     200 = "List of all ProgramYear",
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

        $result = $this->getProgramYearHandler()
            ->findProgramYearsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['programYears'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Create a ProgramYear.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     201 = "Created ProgramYear.",
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
            $programyear = $this->getProgramYearHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_programyears',
                    ['id' => $programyear->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Update a ProgramYear entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     200 = "Updated ProgramYear.",
     *     201 = "Created ProgramYear.",
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
            $programYear = $this->getProgramYearHandler()
                ->findProgramYearBy(['id'=> $id]);
            if ($programYear) {
                $code = Codes::HTTP_OK;
            } else {
                $programYear = $this->getProgramYearHandler()->createProgramYear();
                $code = Codes::HTTP_CREATED;
            }

            $answer['programYear'] =
                $this->getProgramYearHandler()->put(
                    $programYear,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Partial Update to a ProgramYear.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="ProgramYear identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated ProgramYear.",
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
        $answer['programYear'] =
            $this->getProgramYearHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Delete a ProgramYear entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "ProgramYear identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ProgramYear.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ProgramYearInterface $programYear
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $programYear = $this->getOr404($id);

        try {
            $this->getProgramYearHandler()->deleteProgramYear($programYear);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramYearInterface $programYear
     */
    protected function getOr404($id)
    {
        $programYear = $this->getProgramYearHandler()
            ->findProgramYearBy(['id' => $id]);
        if (!$programYear) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $programYear;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('programYear');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return ProgramYearHandler
     */
    protected function getProgramYearHandler()
    {
        return $this->container->get('ilioscore.programyear.handler');
    }
}
