<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
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
        $curriculumInventorySequenceBlockSession = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $curriculumInventorySequenceBlockSession)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['curriculumInventorySequenceBlockSessions'][] = $curriculumInventorySequenceBlockSession;

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

        $manager = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['curriculumInventorySequenceBlockSessions'] =
            $result ? array_values($result) : [];

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
            $handler = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.handler');

            $curriculumInventorySequenceBlockSession = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventorySequenceBlockSession)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.manager');
            $manager->update($curriculumInventorySequenceBlockSession, true, false);

            $answer['curriculumInventorySequenceBlockSessions'] = [$curriculumInventorySequenceBlockSession];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
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
            $manager = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.manager');
            $curriculumInventorySequenceBlockSession = $manager->findOneBy(['id'=> $id]);
            if ($curriculumInventorySequenceBlockSession) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventorySequenceBlockSession = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.handler');

            $curriculumInventorySequenceBlockSession = $handler->put(
                $curriculumInventorySequenceBlockSession,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $curriculumInventorySequenceBlockSession)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($curriculumInventorySequenceBlockSession, true, true);

            $answer['curriculumInventorySequenceBlockSession'] = $curriculumInventorySequenceBlockSession;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
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

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $curriculumInventorySequenceBlockSession)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.manager');
            $manager->delete($curriculumInventorySequenceBlockSession);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
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
        $manager = $this->container->get('ilioscore.curriculuminventorysequenceblocksession.manager');
        $curriculumInventorySequenceBlockSession = $manager->findOneBy(['id' => $id]);
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
        if ($request->request->has('curriculumInventorySequenceBlockSession')) {
            return $request->request->get('curriculumInventorySequenceBlockSession');
        }

        return $request->request->all();
    }
}
