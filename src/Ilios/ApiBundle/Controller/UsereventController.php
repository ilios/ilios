<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UsereventController
 */
class UsereventController extends AbstractController
{
    /**
     * Get events for a user
     *
     * @param string $version
     * @param int $id of the user
     * @param Request $request
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserManager $manager
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function getAction(
        $version,
        $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserManager $manager,
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage
    ) {
        /** @var UserInterface $user */
        $user = $manager->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted('view', $user)) {
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

        $events = array_filter($events, function ($entity) use ($authorizationChecker) {
            return $authorizationChecker->isGranted('view', $entity);
        });
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $result = $manager->addInstructorsToEvents($events);
        $result = $manager->addMaterialsToEvents($result);

        //Remove all draft data when not viewing your own events
        //Un-privileged users get less data
        if ($sessionUser->getId() != $user->getId() ||
            !$sessionUser->hasRole(['Faculty', 'Course Director', 'Developer'])
        ) {
            $now = new \DateTime();
            /* @var UserEvent $event */
            foreach ($events as $event) {
                $event->removeMaterialsInDraft();
                $event->clearTimedMaterials($now);
                $event->clearDataForDraftOrScheduledEvent();
            }
        }

        $response['userEvents'] = $result ? array_values($result) : [];
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
