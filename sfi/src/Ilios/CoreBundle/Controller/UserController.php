<?php

namespace Ilios\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\RestBundle\Controller\Annotations\View;

use Ilios\CoreBundle\Model\UserInterface;

/**
 * Class UserController
 * @package Ilios\CoreBundle\Controller
 * @author Victor Passapera <vpassapera@gmail.com>
 *
 * @Route('/user')
 */
class UserController extends BaseController
{
    /**
     * @Method({"GET"})
     * @Route("/", name="get_users", options={"expose"=true})
     * @View(templateVar="users")
     *
     * @return UserInterface[]
     */
    public function getUsersAction()
    {
        $users = $this->getUserManager()->findUsersBy([]);
        return $users;
    }

    /**
     * @Method({"GET"})
     * @Route("/{userId}", name="get_user", options={"expose"=true})
     * @ParamConverter("user", class="IliosCoreBundle:User", options={"userId":"userId"})
     * @View(templateVar="user")
     *
     * @param UserInterface $user
     * @return UserInterface
     */
    public function getUserAction(UserInterface $user = null)
    {
        return $user;
    }

    /**
     * @Method({"POST"})
     * @Route("/{userId}", name="get_user", options={"expose"=true})
     * @ParamConverter("user", class="IliosCoreBundle:User", options={"userId":"userId"})
     *
     * @param UserInterface $user
     */
    public function postUserAction(UserInterface $user = null)
    {

    }

    /**
     * @Method({"PUT"})
     * @Route("/{userId}", name="get_user", options={"expose"=true})
     * @ParamConverter("user", class="IliosCoreBundle:User", options={"userId":"userId"})
     *
     * @param UserInterface $user
     */
    public function putUserAction(UserInterface $user = null)
    {

    }

    /**
     * @Method({"DELETE"})
     * @Route("/{userId}", name="get_user", options={"expose"=true})
     * @ParamConverter("user", class="IliosCoreBundle:User", options={"userId":"userId"})
     *
     * @param UserInterface $user
     */
    public function deleteUserAction(UserInterface $user = null)
    {

    }
}
