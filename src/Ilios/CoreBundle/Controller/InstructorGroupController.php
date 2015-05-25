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
use Ilios\CoreBundle\Handler\InstructorGroupHandler;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("InstructorGroups")
 */
class InstructorGroupController extends FOSRestController
{
    /**
     * Get a InstructorGroup
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Get a InstructorGroup.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="InstructorGroup identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes={
     *     200 = "InstructorGroup.",
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
        $answer['instructorGroup'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Get all InstructorGroup.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes = {
     *     200 = "List of all InstructorGroup",
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

        $result = $this->getInstructorGroupHandler()
            ->findInstructorGroupsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['instructorGroups'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Create a InstructorGroup.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructorGroupType",
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes={
     *     201 = "Created InstructorGroup.",
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
            $instructorgroup = $this->getInstructorGroupHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_instructorgroups',
                    ['id' => $instructorgroup->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Update a InstructorGroup entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructorGroupType",
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes={
     *     200 = "Updated InstructorGroup.",
     *     201 = "Created InstructorGroup.",
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
            $instructorGroup = $this->getInstructorGroupHandler()
                ->findInstructorGroupBy(['id'=> $id]);
            if ($instructorGroup) {
                $code = Codes::HTTP_OK;
            } else {
                $instructorGroup = $this->getInstructorGroupHandler()->createInstructorGroup();
                $code = Codes::HTTP_CREATED;
            }

            $answer['instructorGroup'] =
                $this->getInstructorGroupHandler()->put(
                    $instructorGroup,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Partial Update to a InstructorGroup.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructorGroupType",
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="InstructorGroup identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated InstructorGroup.",
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
        $answer['instructorGroup'] =
            $this->getInstructorGroupHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Delete a InstructorGroup entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "InstructorGroup identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted InstructorGroup.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal InstructorGroupInterface $instructorGroup
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $instructorGroup = $this->getOr404($id);

        try {
            $this->getInstructorGroupHandler()->deleteInstructorGroup($instructorGroup);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return InstructorGroupInterface $instructorGroup
     */
    protected function getOr404($id)
    {
        $instructorGroup = $this->getInstructorGroupHandler()
            ->findInstructorGroupBy(['id' => $id]);
        if (!$instructorGroup) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $instructorGroup;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('instructorGroup');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return InstructorGroupHandler
     */
    protected function getInstructorGroupHandler()
    {
        return $this->container->get('ilioscore.instructorgroup.handler');
    }
}
