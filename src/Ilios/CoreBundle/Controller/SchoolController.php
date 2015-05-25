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
use Ilios\CoreBundle\Handler\SchoolHandler;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Schools")
 */
class SchoolController extends FOSRestController
{
    /**
     * Get a School
     *
     * @ApiDoc(
     *   section = "School",
     *   description = "Get a School.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="School identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes={
     *     200 = "School.",
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
        $answer['school'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all School.
     *
     * @ApiDoc(
     *   section = "School",
     *   description = "Get all School.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes = {
     *     200 = "List of all School",
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

        $result = $this->getSchoolHandler()
            ->findSchoolsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['schools'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a School.
     *
     * @ApiDoc(
     *   section = "School",
     *   description = "Create a School.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SchoolType",
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes={
     *     201 = "Created School.",
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
            $school = $this->getSchoolHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_schools',
                    ['id' => $school->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a School.
     *
     * @ApiDoc(
     *   section = "School",
     *   description = "Update a School entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SchoolType",
     *   output="Ilios\CoreBundle\Entity\School",
     *   statusCodes={
     *     200 = "Updated School.",
     *     201 = "Created School.",
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
            $school = $this->getSchoolHandler()
                ->findSchoolBy(['id'=> $id]);
            if ($school) {
                $code = Codes::HTTP_OK;
            } else {
                $school = $this->getSchoolHandler()->createSchool();
                $code = Codes::HTTP_CREATED;
            }

            $answer['school'] =
                $this->getSchoolHandler()->put(
                    $school,
                    $this->getPostData($request)
                );
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
     *   section = "School",
     *   description = "Partial Update to a School.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SchoolType",
     *   output="Ilios\CoreBundle\Entity\School",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="School identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated School.",
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
        $answer['school'] =
            $this->getSchoolHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a School.
     *
     * @ApiDoc(
     *   section = "School",
     *   description = "Delete a School entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal SchoolInterface $school
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return SchoolInterface $school
     */
    protected function getOr404($id)
    {
        $school = $this->getSchoolHandler()
            ->findSchoolBy(['id' => $id]);
        if (!$school) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $school;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('school');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return SchoolHandler
     */
    protected function getSchoolHandler()
    {
        return $this->container->get('ilioscore.school.handler');
    }
}
