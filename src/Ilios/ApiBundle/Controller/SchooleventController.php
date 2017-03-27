<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;

/**
 * Class SchooleventController
 *
 * Search for events happening in a school
 * @package Ilios\ApiBundle\Controller
 */
class SchooleventController extends Controller
{
    /**
     * @param string $version of the API requested
     * @param string $id of the school
     * @param Request $request
     *
     * @return Response
     */
    public function getAction($version, $id, Request $request)
    {
        $schoolManager = $this->container->get('ilioscore.school.manager');
        $userManager = $this->container->get('ilioscore.user.manager');

        $school = $schoolManager->findOneBy(['id' => $id]);

        if (!$school) {
            throw new NotFoundHttpException(sprintf('The school \'%s\' was not found.', $id));
        }

        $fromTimestamp = $request->get('from');
        $toTimestamp = $request->get('to');
        $from = DateTime::createFromFormat('U', $fromTimestamp);
        $to = DateTime::createFromFormat('U', $toTimestamp);

        if (!$from) {
            throw new InvalidInputWithSafeUserMessageException("?from is missing or is not a valid timestamp");
        }
        if (!$to) {
            throw new InvalidInputWithSafeUserMessageException("?to is missing or is not a valid timestamp");
        }
        $events = $schoolManager->findEventsForSchool($school->getId(), $from, $to);

        $authChecker = $this->get('security.authorization_checker');
        $events = array_filter($events, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        $user = $this->get('security.token_storage')->getToken()->getUser();
        //Un-privileged users get less data
        if (!$user->hasRole(['Faculty', 'Course Director', 'Developer'])) {
            /** @var SchoolEvent $event */
            foreach ($events as $event) {
                $event->clearDataForScheduledEvent();
            }
        }

        $result = $userManager->addInstructorsToEvents($events);

        $response['events'] = $result ? array_values($result) : [];
        $serializer = $this->get('ilios_api.serializer');
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
