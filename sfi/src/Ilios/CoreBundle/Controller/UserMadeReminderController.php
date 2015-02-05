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
use Ilios\CoreBundle\Handler\UserMadeReminderHandler;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * UserMadeReminder controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("UserMadeReminder")
 */
class UserMadeReminderController extends FOSRestController
{
    
    /**
     * Get a UserMadeReminder
     *
     * @ApiDoc(
     *   description = "Get a UserMadeReminder.",
     *   resource = true,
     *   requirements={
     *     {"name"="userMadeReminderId", "dataType"="integer", "requirement"="", "description"="UserMadeReminder identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     200 = "UserMadeReminder.",
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
        $answer['userMadeReminder'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all UserMadeReminder.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all UserMadeReminder.",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes = {
     *     200 = "List of all UserMadeReminder",
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

        $answer['userMadeReminder'] =
            $this->getUserMadeReminderHandler()->findUserMadeRemindersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['userMadeReminder']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a UserMadeReminder.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a UserMadeReminder.",
     *   input="Ilios\CoreBundle\Form\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     201 = "Created UserMadeReminder.",
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
            $new  =  $this->getUserMadeReminderHandler()->post($request->request->all());
            $answer['userMadeReminder'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a UserMadeReminder.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a UserMadeReminder entity.",
     *   input="Ilios\CoreBundle\Form\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     200 = "Updated UserMadeReminder.",
     *     201 = "Created UserMadeReminder.",
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
            if ($userMadeReminder = $this->getUserMadeReminderHandler()->findUserMadeReminderBy(['userMadeReminderId'=> $id])) {
                $answer['userMadeReminder']= $this->getUserMadeReminderHandler()->put($userMadeReminder, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['userMadeReminder'] = $this->getUserMadeReminderHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a UserMadeReminder.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a UserMadeReminder.",
     *   input="Ilios\CoreBundle\Form\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   requirements={
     *     {"name"="userMadeReminderId", "dataType"="integer", "requirement"="", "description"="UserMadeReminder identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated UserMadeReminder.",
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
        $answer['userMadeReminder'] = $this->getUserMadeReminderHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a UserMadeReminder.
     *
     * @ApiDoc(
     *   description = "Delete a UserMadeReminder entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "userMadeReminderId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "UserMadeReminder identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted UserMadeReminder.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal UserMadeReminderInterface $userMadeReminder
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $userMadeReminder = $this->getOr404($id);
        try {
            $this->getUserMadeReminderHandler()->deleteUserMadeReminder($userMadeReminder);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return UserMadeReminderInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getUserMadeReminderHandler()->findUserMadeReminderBy(['userMadeReminderId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return UserMadeReminderHandler
     */
    public function getUserMadeReminderHandler()
    {
        return $this->container->get('ilioscore.usermadereminder.handler');
    }
}
