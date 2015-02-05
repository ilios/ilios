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
use Ilios\CoreBundle\Handler\DisciplineHandler;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Discipline controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Discipline")
 */
class DisciplineController extends FOSRestController
{
    
    /**
     * Get a Discipline
     *
     * @ApiDoc(
     *   description = "Get a Discipline.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Discipline identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes={
     *     200 = "Discipline.",
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
        $answer['discipline'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Discipline.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Discipline.",
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes = {
     *     200 = "List of all Discipline",
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

        $answer['discipline'] =
            $this->getDisciplineHandler()->findDisciplinesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['discipline']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Discipline.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Discipline.",
     *   input="Ilios\CoreBundle\Form\DisciplineType",
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes={
     *     201 = "Created Discipline.",
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
            $new  =  $this->getDisciplineHandler()->post($request->request->all());
            $answer['discipline'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Discipline.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Discipline entity.",
     *   input="Ilios\CoreBundle\Form\DisciplineType",
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes={
     *     200 = "Updated Discipline.",
     *     201 = "Created Discipline.",
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
            if ($discipline = $this->getDisciplineHandler()->findDisciplineBy(['id'=> $id])) {
                $answer['discipline']= $this->getDisciplineHandler()->put($discipline, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['discipline'] = $this->getDisciplineHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Discipline.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Discipline.",
     *   input="Ilios\CoreBundle\Form\DisciplineType",
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Discipline identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Discipline.",
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
        $answer['discipline'] = $this->getDisciplineHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Discipline.
     *
     * @ApiDoc(
     *   description = "Delete a Discipline entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Discipline identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Discipline.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal DisciplineInterface $discipline
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $discipline = $this->getOr404($id);
        try {
            $this->getDisciplineHandler()->deleteDiscipline($discipline);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return DisciplineInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getDisciplineHandler()->findDisciplineBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return DisciplineHandler
     */
    public function getDisciplineHandler()
    {
        return $this->container->get('ilioscore.discipline.handler');
    }
}
