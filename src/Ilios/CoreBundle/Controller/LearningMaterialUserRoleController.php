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
use Ilios\CoreBundle\Handler\LearningMaterialUserRoleHandler;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LearningMaterialUserRoleController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("LearningMaterialUserRoles")
 */
class LearningMaterialUserRoleController extends FOSRestController
{
    /**
     * Get a LearningMaterialUserRole
     *
     * @ApiDoc(
     *   section = "LearningMaterialUserRole",
     *   description = "Get a LearningMaterialUserRole.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="LearningMaterialUserRole identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\LearningMaterialUserRole",
     *   statusCodes={
     *     200 = "LearningMaterialUserRole.",
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
        $answer['learningMaterialUserRole'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all LearningMaterialUserRole.
     *
     * @ApiDoc(
     *   section = "LearningMaterialUserRole",
     *   description = "Get all LearningMaterialUserRole.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\LearningMaterialUserRole",
     *   statusCodes = {
     *     200 = "List of all LearningMaterialUserRole",
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

        $result = $this->getLearningMaterialUserRoleHandler()
            ->findLearningMaterialUserRolesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['learningMaterialUserRoles'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a LearningMaterialUserRole.
     *
     * @ApiDoc(
     *   section = "LearningMaterialUserRole",
     *   description = "Create a LearningMaterialUserRole.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialUserRoleType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialUserRole",
     *   statusCodes={
     *     201 = "Created LearningMaterialUserRole.",
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
            $learningmaterialuserrole = $this->getLearningMaterialUserRoleHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_learningmaterialuserroles',
                    ['id' => $learningmaterialuserrole->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearningMaterialUserRole.
     *
     * @ApiDoc(
     *   section = "LearningMaterialUserRole",
     *   description = "Update a LearningMaterialUserRole entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialUserRoleType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialUserRole",
     *   statusCodes={
     *     200 = "Updated LearningMaterialUserRole.",
     *     201 = "Created LearningMaterialUserRole.",
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
            $learningMaterialUserRole = $this->getLearningMaterialUserRoleHandler()
                ->findLearningMaterialUserRoleBy(['id'=> $id]);
            if ($learningMaterialUserRole) {
                $code = Codes::HTTP_OK;
            } else {
                $learningMaterialUserRole = $this->getLearningMaterialUserRoleHandler()->createLearningMaterialUserRole();
                $code = Codes::HTTP_CREATED;
            }

            $answer['learningMaterialUserRole'] =
                $this->getLearningMaterialUserRoleHandler()->put(
                    $learningMaterialUserRole,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a LearningMaterialUserRole.
     *
     * @ApiDoc(
     *   section = "LearningMaterialUserRole",
     *   description = "Partial Update to a LearningMaterialUserRole.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialUserRoleType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialUserRole",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="LearningMaterialUserRole identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated LearningMaterialUserRole.",
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
        $answer['learningMaterialUserRole'] =
            $this->getLearningMaterialUserRoleHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a LearningMaterialUserRole.
     *
     * @ApiDoc(
     *   section = "LearningMaterialUserRole",
     *   description = "Delete a LearningMaterialUserRole entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "LearningMaterialUserRole identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted LearningMaterialUserRole.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal LearningMaterialUserRoleInterface $learningMaterialUserRole
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return LearningMaterialUserRoleInterface $learningMaterialUserRole
     */
    protected function getOr404($id)
    {
        $learningMaterialUserRole = $this->getLearningMaterialUserRoleHandler()
            ->findLearningMaterialUserRoleBy(['id' => $id]);
        if (!$learningMaterialUserRole) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $learningMaterialUserRole;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('learningMaterialUserRole');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return LearningMaterialUserRoleHandler
     */
    protected function getLearningMaterialUserRoleHandler()
    {
        return $this->container->get('ilioscore.learningmaterialuserrole.handler');
    }
}
