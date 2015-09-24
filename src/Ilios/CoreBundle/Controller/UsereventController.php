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
use DateTime;
use Exception;

/**
 * User event controller
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Userevent")
 */
class UsereventController extends FOSRestController
{

  /**
   * Get events for a user
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Get events for a user.",
   *   output="Ilios\CoreBundle\Classes\UserEvent",
   *   statusCodes = {
   *     200 = "List of user events",
   *     204 = "No content. Nothing to list."
   *   }
   * )
   *
   * @View(serializerEnableMaxDepthChecks=true)
   *
   * @param integer $id
   * @param ParamFetcherInterface $paramFetcher
   *
   * @return Response
   *
   * @QueryParam(
   *   name="from",
   *   requirements="\d+",
   *   description="Timestamp for first event from time."
   * )
   * @QueryParam(
   *   name="to",
   *   requirements="\d+",
   *   description="Time stamp for last event from time"
   * )
   */
    public function getAction($id, ParamFetcherInterface $paramFetcher)
    {
        $userHandler = $this->container->get('ilioscore.user.handler');

        $user = $userHandler->findUserBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $fromTimestamp = $paramFetcher->get('from');
        $toTimestamp = $paramFetcher->get('to');
        $from = DateTime::createFromFormat('U', $fromTimestamp);
        $to = DateTime::createFromFormat('U', $toTimestamp);
        if (!$from) {
            throw new Exception("'from' is not a valid timstamp");
        }
        if (!$to) {
            throw new Exception("'to' is not a valid timstamp");
        }
        $result = $userHandler->findEventsForUser(
            $user->getId(),
            $from,
            $to
        );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['userEvents'] = $result ? array_values($result) : [];

        return $answer;
    }
}
