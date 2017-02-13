<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Ilios\CoreBundle\Exception\InvalidFormException;

use Ilios\CoreBundle\Classes\CurrentSession;
use Ilios\CoreBundle\Entity\User;

class CurrentsessionController
{

    /**
     * Get the current session,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets the current session",
     *   output = "Ilios\CoreBundle\Classes\CurrentSession",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the session is not found"
     *   }
     * )
     *
     *
     * @return Response
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function getCurrentsessionAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('No current session');
        }
        $sess = new CurrentSession($user);
        $view = $this->view(array('currentsession' => $sess), Codes::HTTP_OK)
                ->setTemplate("IliosCoreBundle:Currentsession:getCurrentsession.html.twig")
                ->setTemplateVar('currentsession')
        ;

        return $this->handleView($view);
    }
}
