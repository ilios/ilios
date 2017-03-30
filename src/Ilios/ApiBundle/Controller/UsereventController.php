<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;

/**
 * Class UsereventController
 * @package Ilios\ApiBundle\Controller
 */
class UsereventController extends Controller
{
    /**
     * Get events for a user
     *
     * @param string $version
     * @param int $id of the user
     * @param Request $request
     *
     * @return Response
     */
    public function getAction($version, $id, Request $request)
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
        $events = $manager->findEventsForUser($user->getId(), $from, $to);

        $authChecker = $this->get('security.authorization_checker');
        $events = array_filter($events, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        $result = $manager->addInstructorsToEvents($events);
        $result = $manager->addMaterialsToEvents($result);

        //Un-privileged users get less data
        if (!$user->hasRole(['Faculty', 'Course Director', 'Developer'])) {
            /* @var UserEvent $event */
            foreach ($events as $event) {
                $event->clearDataForScheduledEvent();
            }
        }

        $response['userEvents'] = $result ? array_values($result) : [];
        $serializer = $this->get('ilios_api.serializer');
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
