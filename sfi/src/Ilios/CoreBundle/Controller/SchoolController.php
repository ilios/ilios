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
use Ilios\CoreBundle\Handler\SchoolHandler;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * School controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("School")
 */
class SchoolController extends FOSRestController
{
    
    /**
     * Get a School
     *
     * @ApiDoc(
     *   description = "Get a School.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="School identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes={
     *     200 = "School.",
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
        $answer['school'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all School.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all School.",
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes = {
     *     200 = "List of all School",
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

        $answer['school'] =
            $this->getSchoolHandler()->findSchoolsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['school']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a School.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a School.",
     *   input="Ilios\CoreBundle\Form\SchoolType",
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes={
     *     201 = "Created School.",
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
            $new  =  $this->getSchoolHandler()->post($request->request->all());
            $answer['school'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a School.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a School entity.",
     *   input="Ilios\CoreBundle\Form\SchoolType",
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes={
     *     200 = "Updated School.",
     *     201 = "Created School.",
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
            if ($school = $this->getSchoolHandler()->findSchoolBy(['id'=> $id])) {
                $answer['school']= $this->getSchoolHandler()->put($school, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['school'] = $this->getSchoolHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a School.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a School.",
     *   input="Ilios\CoreBundle\Form\SchoolType",
     *   output="Ilios\CoreBundle\Entity\School",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="School identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated School.",
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
        $answer['school'] = $this->getSchoolHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a School.
     *
     * @ApiDoc(
     *   description = "Delete a School entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "School identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted School.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal SchoolInterface $school
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $school = $this->getOr404($id);
        try {
            $this->getSchoolHandler()->deleteSchool($school);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return SchoolInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getSchoolHandler()->findSchoolBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return SchoolHandler
     */
    public function getSchoolHandler()
    {
        return $this->container->get('ilioscore.school.handler');
    }
}
