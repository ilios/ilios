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
use Ilios\CoreBundle\Handler\LearnerGroupHandler;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("LearnerGroups")
 */
class LearnerGroupController extends FOSRestController
{
    /**
     * Get a LearnerGroup
     *
     * @ApiDoc(
     *   section = "LearnerGroup",
     *   description = "Get a LearnerGroup.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="LearnerGroup identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes={
     *     200 = "LearnerGroup.",
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
        $answer['learnerGroup'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all LearnerGroup.
     *
     * @ApiDoc(
     *   section = "LearnerGroup",
     *   description = "Get all LearnerGroup.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes = {
     *     200 = "List of all LearnerGroup",
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

        $result = $this->getLearnerGroupHandler()
            ->findLearnerGroupsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['learnerGroups'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a LearnerGroup.
     *
     * @ApiDoc(
     *   section = "LearnerGroup",
     *   description = "Create a LearnerGroup.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearnerGroupType",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes={
     *     201 = "Created LearnerGroup.",
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
            $learnergroup = $this->getLearnerGroupHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_learnergroups',
                    ['id' => $learnergroup->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearnerGroup.
     *
     * @ApiDoc(
     *   section = "LearnerGroup",
     *   description = "Update a LearnerGroup entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearnerGroupType",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes={
     *     200 = "Updated LearnerGroup.",
     *     201 = "Created LearnerGroup.",
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
            $learnerGroup = $this->getLearnerGroupHandler()
                ->findLearnerGroupBy(['id'=> $id]);
            if ($learnerGroup) {
                $code = Codes::HTTP_OK;
            } else {
                $learnerGroup = $this->getLearnerGroupHandler()->createLearnerGroup();
                $code = Codes::HTTP_CREATED;
            }

            $answer['learnerGroup'] =
                $this->getLearnerGroupHandler()->put(
                    $learnerGroup,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a LearnerGroup.
     *
     * @ApiDoc(
     *   section = "LearnerGroup",
     *   description = "Partial Update to a LearnerGroup.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearnerGroupType",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="LearnerGroup identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated LearnerGroup.",
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
        $answer['learnerGroup'] =
            $this->getLearnerGroupHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a LearnerGroup.
     *
     * @ApiDoc(
     *   section = "LearnerGroup",
     *   description = "Delete a LearnerGroup entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "LearnerGroup identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted LearnerGroup.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal LearnerGroupInterface $learnerGroup
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $learnerGroup = $this->getOr404($id);

        try {
            $this->getLearnerGroupHandler()->deleteLearnerGroup($learnerGroup);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return LearnerGroupInterface $learnerGroup
     */
    protected function getOr404($id)
    {
        $learnerGroup = $this->getLearnerGroupHandler()
            ->findLearnerGroupBy(['id' => $id]);
        if (!$learnerGroup) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $learnerGroup;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('learnerGroup');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return LearnerGroupHandler
     */
    protected function getLearnerGroupHandler()
    {
        return $this->container->get('ilioscore.learnergroup.handler');
    }
}
