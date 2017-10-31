<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class SchooleventController
 *
 * Search for events happening in a school
 */
class SchooleventController extends Controller
{
    /**
     * @param string $version of the API requested
     * @param string $id of the school
     * @param Request $request
     * @param SchoolManager $schoolManager
     * @param UserManager $userManager
     * @param AuthorizationCheckerInterface $authorizationChecker,
     * @param TokenStorageInterface $tokenStorage,
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function getAction(
        $version,
        $id,
        Request $request,
        SchoolManager $schoolManager,
        UserManager $userManager,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ) {
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

        $events = array_filter($events, function ($entity) use ($authorizationChecker) {
            return $authorizationChecker->isGranted('view', $entity);
        });

        $result = $userManager->addInstructorsToEvents($events);
        $result = $userManager->addMaterialsToEvents($result);

        $user = $tokenStorage->getToken()->getUser();

        //Un-privileged users get less data
        if (!$user->hasRole(['Faculty', 'Course Director', 'Developer'])) {
            /** @var SchoolEvent $event */
            $now = new \DateTime();
            foreach ($events as $event) {
                $event->removeMaterialsInDraft();
                $event->clearTimedMaterials($now);
                $event->clearDataForDraftOrScheduledEvent();
            }
        }

        $response['events'] = $result ? array_values($result) : [];
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
