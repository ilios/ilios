<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Util\Codes;

use Ilios\CoreBundle\Form\UserType;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Model\UserInterface;

/**
 * prefix: /v1 name_prefix: api_1_
 * Class UserController
 * @package Ilios\CoreBundle\Controller
 *
 * @Route("/users")
 * @Prefix("v1")
 * @NamePrefix("api_1_")
 */
class UserController extends BaseController
{
    /**
     * Get a single user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a User for a given id",
     *   output = "Ilios\CoreBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Get("/{userId}.{_format}", defaults={"_format": "html"}, name="get_user", options={"exposed"=true})
     * @ParamConverter("user", class="IliosCoreBundle:User", options={"userId":"userId"})
     * @View(templateVar="user")
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     * @throws NotFoundHttpException
     */
    public function getUserAction(UserInterface $user = null)
    {
        //Is the current user allowed to make this request?
        return $user;
    }

    /**
     * List all users
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lists all the users",
     *   output = "Ilios\CoreBundle\Entity\User"
     * )
     *
     * @Get(".{_format}", defaults={"_format": "html"}, name="get_users", options={"exposed"=true})
     * @View(templateVar="users")
     *
     * @return UserInterface[]
     */
    public function getUsersAction()
    {
        $users = $this->getUserManager()->findUsersBy(array());
        return $users;
    }

    /**
     * Create an User from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new user from the submitted data.",
     *   input = "Ilios\CoreBundle\Form\UserType",
     *   output = "Ilios\CoreBundle\Entity\User",
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request
     * @param UserInterface $user
     *
     * @return Response
     */
    public function postUserAction(Request $request, UserInterface $user)
    {

        try {
            $obj = $this->container->get('ilios_core.user_handler')->post(
                $request->request->get(UserType::NAME)
            );

            $view = $this->view(array('user' => $obj), Codes::HTTP_CREATED)
                    ->setTemplate("IliosCoreBundle:User:getUser.html.twig")
                    ->setTemplateVar('user')
            ;

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {

            return $this->handleFormException($exception);
        }
    }

    /**
     * Presents the form to use to create a new user.
     *
     * @return Response
     */
    public function newUserAction()
    {
        $form = $this->createForm(
            new UserType(),
            null,
            array(
                'action' => $this->generateUrl('api_1_post_user')
            )
        );
        $view = $this->view(array('form' => $form))
                ->setTemplate("IliosCoreBundle:User:newUser.html.twig")
                ->setTemplateVar('form')
        ;

        return $this->handleView($view);
    }

    /**
     * Update existing user from the submitted data or create a new user
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Ilios\CoreBundle\Form\UserType",
     *   output = "Ilios\CoreBundle\Entity\User",
     *   statusCodes = {
     *     201 = "Returned when the User is created",
     *     202 = "Returned when updated",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the user id
     *
     * @return Response
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function putUserAction(Request $request, $id)
    {
        try {
            if (!($user = $this->container->get('ilios_core.user_handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $user = $this->container->get('ilios_core.user_handler')->post(
                    $request->request->get(UserType::NAME)
                );
            } else {
                $statusCode = Codes::HTTP_ACCEPTED;
                $handler = $this->container->get('ilios_core.user_handler');
                $user = $handler->put(
                    $user,
                    $request->request->get(UserType::NAME)
                );
            }

            $view = $this->view(array('user' => $user), $statusCode)
                    ->setTemplate("IliosCoreBundle:User:getUser.html.twig")
                    ->setTemplateVar('user')
            ;

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            
            return $this->handleFormException($exception);
        }
    }

    private function processForm(UserInterface $user)
    {
        $statusCode = $user->getUserId() ? Codes::HTTP_CREATED : Codes::HTTP_NO_CONTENT;

        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('api_1_post_user')
        ));
    }
    /**
     * Generate a response for form validation errors
     * 
     * @param \Ilios\CoreBundle\Exception\InvalidFormException $exception
     * @return Response
     */
    protected function handleFormException(InvalidFormException $exception)
    {
        $form = $exception->getForm();
        $view = $this->view($form, Codes::HTTP_BAD_REQUEST)
            ->setTemplate("IliosCoreBundle:User:newUser.html.twig")
            ->setTemplateVar('form')
        ;
        
        return $this->handleView($view);
    }
}
