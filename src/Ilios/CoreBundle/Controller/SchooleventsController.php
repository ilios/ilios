<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use DateTime;

/**
 * School event.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Schoolevents")
 */
class SchooleventsController extends FOSRestController
{

  /**
   * Get events for a school
   *
   * @ApiDoc(
   *   resource = true,
   *   description = "Get events for a school.",
   *   output="Ilios\CoreBundle\Classes\SchoolEvent",
   *   statusCodes = {
   *     200 = "List of school events",
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
        $schoolHandler = $this->container->get('ilioscore.school.handler');
        $userHandler = $this->container->get('ilioscore.user.handler');

        $school = $schoolHandler->findSchoolBy(['id' => $id]);

        if (!$school) {
            throw new NotFoundHttpException(sprintf('The school \'%s\' was not found.', $id));
        }
        $fromTimestamp = $paramFetcher->get('from');
        $toTimestamp = $paramFetcher->get('to');
        $from = DateTime::createFromFormat('U', $fromTimestamp);
        $to = DateTime::createFromFormat('U', $toTimestamp);
        // MOST TERRIBLE KLUDGE!
        // Offset date range parameters for ILMs.
        // [ST 2015/12/22]
        $fromIlm = DateTime::createFromFormat('U', $fromTimestamp);
        $fromIlm->setTimezone(new \DateTimeZone($this->container->getParameter('ilios_core.frontend_timezone')));
        $toIlm = DateTime::createFromFormat('U', $toTimestamp);
        $toIlm->setTimezone(new \DateTimeZone($this->container->getParameter('ilios_core.frontend_timezone')));

        if (!$from) {
            throw new InvalidInputWithSafeUserMessageException("?from is missing or is not a valid timestamp");
        }
        if (!$to) {
            throw new InvalidInputWithSafeUserMessageException("?to is missing or is not a valid timestamp");
        }
        $result = $userHandler->addInstructorsToEvents(
            $schoolHandler->findEventsForSchool(
                $school->getId(),
                $from,
                $to,
                $fromIlm,
                $toIlm
            )
        );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['events'] = $result ? array_values($result) : [];

        return $answer;
    }
}
