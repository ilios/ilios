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

        $school = $schoolHandler->findSchoolBy(['id' => $id]);

        if (!$school) {
            throw new NotFoundHttpException(sprintf('The school \'%s\' was not found.', $id));
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
        $result = $schoolHandler->findEventsForSchool(
            $school->getId(),
            $from,
            $to
        );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['events'] = $result ? $result : new ArrayCollection([]);

        return $answer;
    }
}
