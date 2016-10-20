<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use DateTime;

/**
 * User materials controller
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Usermaterials")
 */
class UsermaterialsController extends FOSRestController
{

  /**
   * Get events for a user
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Get materials for a user.",
   *   output="Ilios\CoreBundle\Classes\UserMaterial",
   *   statusCodes = {
   *     200 = "List of user materials",
   *     204 = "No content. Nothing to list."
   *   },
   *   tags = {
   *     "beta"
   *   }
   * )
   *
   * @View(serializerEnableMaxDepthChecks=true)
   *
   * @param integer $id
   *
   * @return Response
   *
   *
   * @throws \Exception
   */
    public function getAction($id)
    {
        $manager = $this->container->get('ilioscore.user.manager');

        $user = $manager->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $materials = $manager->findMaterialsForUser($user->getId());

        //If there are no matches return an empty array
        $answer['userMaterials'] = $materials ? array_values($materials) : [];

        return $answer;
    }
}
