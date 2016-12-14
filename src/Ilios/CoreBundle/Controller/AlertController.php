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
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Class AlertController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Alerts")
 */
class AlertController extends FOSRestController
{
    /**
     * Get a Alert
     *
     * @ApiDoc(
     *   section = "Alert",
     *   description = "Get a Alert.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Alert identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes={
     *     200 = "Alert.",
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
        $manager = $this->container->get('ilioscore.alert.manager');

        $alert = $manager->findDTOBy(['id' => $id]);
        if (!$alert) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $alert)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['alerts'][] = $alert;

        return $answer;
    }

    /**
     * Get all Alert.
     *
     * @ApiDoc(
     *   section = "Alert",
     *   description = "Get all Alert.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes = {
     *     200 = "List of all Alert",
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

        $manager = $this->container->get('ilioscore.alert.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['alerts'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Alert.
     *
     * @ApiDoc(
     *   section = "Alert",
     *   description = "Create a Alert.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AlertType",
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes={
     *     201 = "Created Alert.",
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
            $handler = $this->container->get('ilioscore.alert.handler');
            $alert = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $alert)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.alert.manager');
            $manager->update($alert, true, false);

            $answer['alerts'] = [$alert];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Alert.
     *
     * @ApiDoc(
     *   section = "Alert",
     *   description = "Update a Alert entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AlertType",
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes={
     *     200 = "Updated Alert.",
     *     201 = "Created Alert.",
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
            $manager = $this->container->get('ilioscore.alert.manager');
            $alert = $manager->findOneBy(['id'=> $id]);
            if ($alert) {
                $code = Codes::HTTP_OK;
            } else {
                $alert = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.alert.handler');
            $alert = $handler->put($alert, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $alert)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($alert, true, true);

            $answer['alert'] = $alert;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Alert.
     *
     * @ApiDoc(
     *   section = "Alert",
     *   description = "Delete a Alert entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Alert identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Alert.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AlertInterface $alert
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $alert = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $alert)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.alert.manager');
            $manager->delete($alert);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AlertInterface $alert
     */
    protected function getOr404($id)
    {
        $manager =$this->container->get('ilioscore.alert.manager');
        $alert = $manager->findOneBy(['id' => $id]);
        if (!$alert) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $alert;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('alert')) {
            return $request->request->get('alert');
        }

        return $request->request->all();
    }
}
