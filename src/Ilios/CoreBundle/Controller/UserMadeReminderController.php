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
use Ilios\CoreBundle\Handler\UserMadeReminderHandler;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Class UserMadeReminderController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("UserMadeReminders")
 */
class UserMadeReminderController extends FOSRestController
{
    /**
     * Get a UserMadeReminder
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Get a UserMadeReminder.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="UserMadeReminder identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     200 = "UserMadeReminder.",
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
        $answer['userMadeReminder'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Get all UserMadeReminder.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes = {
     *     200 = "List of all UserMadeReminder",
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

        $result = $this->getUserMadeReminderHandler()
            ->findUserMadeRemindersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['userMadeReminders'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Create a UserMadeReminder.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     201 = "Created UserMadeReminder.",
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
            $usermadereminder = $this->getUserMadeReminderHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_usermadereminders',
                    ['id' => $usermadereminder->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Update a UserMadeReminder entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     200 = "Updated UserMadeReminder.",
     *     201 = "Created UserMadeReminder.",
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
            $userMadeReminder = $this->getUserMadeReminderHandler()
                ->findUserMadeReminderBy(['id'=> $id]);
            if ($userMadeReminder) {
                $code = Codes::HTTP_OK;
            } else {
                $userMadeReminder = $this->getUserMadeReminderHandler()->createUserMadeReminder();
                $code = Codes::HTTP_CREATED;
            }

            $answer['userMadeReminder'] =
                $this->getUserMadeReminderHandler()->put(
                    $userMadeReminder,
                    $this->getPostData($request)
                );
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
     *   section = "UserMadeReminder",
     *   description = "Partial Update to a UserMadeReminder.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="UserMadeReminder identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated UserMadeReminder.",
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
        $answer['userMadeReminder'] =
            $this->getUserMadeReminderHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Delete a UserMadeReminder entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal UserMadeReminderInterface $userMadeReminder
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return UserMadeReminderInterface $userMadeReminder
     */
    protected function getOr404($id)
    {
        $userMadeReminder = $this->getUserMadeReminderHandler()
            ->findUserMadeReminderBy(['id' => $id]);
        if (!$userMadeReminder) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $userMadeReminder;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('userMadeReminder');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return UserMadeReminderHandler
     */
    protected function getUserMadeReminderHandler()
    {
        return $this->container->get('ilioscore.usermadereminder.handler');
    }
}
