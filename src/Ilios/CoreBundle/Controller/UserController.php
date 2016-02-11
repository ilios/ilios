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
use Ilios\CoreBundle\Handler\UserHandler;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Users")
 */
class UserController extends FOSRestController
{
    /**
     * Get a User
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Get a User.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="User identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     200 = "User.",
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
        $user = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['users'][] = $user;

        return $answer;
    }

    /**
     * Get all User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Get all User.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes = {
     *     200 = "List of all User",
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
     * @QueryParam(
     *   name="q",
     *   nullable=true,
     *   description="string search term to compare to name and email"
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
        $q = !is_null($paramFetcher->get('q')) ? $paramFetcher->get('q') : false;
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        if ($q) {
            $result = $this->getUserHandler()->findUsersByQ(
                $q,
                $orderBy,
                $limit,
                $offset,
                $criteria
            );
        } else {
            $result = $this->getUserHandler()->findUsersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        }

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['users'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Create a User.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     201 = "Created User.",
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
            $handler = $this->getUserHandler();
            $data = $this->getPostData($request);

            //create an icsFeedKey for the new user
            $random = random_bytes(128);
            $key = microtime() . '_' . $random;
            $data['icsFeedKey'] = hash('sha256', $key);

            $user = $handler->post($data);

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $user)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getUserHandler()->updateUser($user, true, false);

            $answer['users'] = [$user];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Update a User entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     200 = "Updated User.",
     *     201 = "Created User.",
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
            $user = $this->getUserHandler()
                ->findUserBy(['id'=> $id]);
            if ($user) {
                $code = Codes::HTTP_OK;
            } else {
                $user = $this->getUserHandler()
                    ->createUser();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getUserHandler();

            $user = $handler->put(
                $user,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $user)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getUserHandler()->updateUser($user, true, true);

            $answer['user'] = $user;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Delete a User entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "User identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted User.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal UserInterface $user
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $user = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getUserHandler()
                ->deleteUser($user);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return UserInterface $user
     */
    protected function getOr404($id)
    {
        $user = $this->getUserHandler()
            ->findUserBy(['id' => $id]);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $user;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('user')) {
            return $request->request->get('user');
        }

        return $request->request->all();
    }

    /**
     * @return UserHandler
     */
    protected function getUserHandler()
    {
        return $this->container->get('ilioscore.user.handler');
    }
}
