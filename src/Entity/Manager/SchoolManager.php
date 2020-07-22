<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Classes\CalendarEvent;
use App\Classes\SchoolEvent;
use App\Entity\Repository\SchoolRepository;
use App\Service\UserMaterialFactory;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class SchoolManager
 */
class SchoolManager extends V1CompatibleBaseManager
{
    /**
     * @var UserMaterialFactory
     */
    protected UserMaterialFactory $factory;

    /**
     * @param ManagerRegistry $registry
     * @param string $class
     * @param UserMaterialFactory $factory
     */
    public function __construct(ManagerRegistry $registry, $class, UserMaterialFactory $factory)
    {
        parent::__construct($registry, $class);
        $this->factory = $factory;
    }

    /**
     * @param int $schoolId
     * @param DateTime $from
     * @param DateTime $to
     * @return SchoolEvent[]
     * @throws Exception
     */
    public function findEventsForSchool($schoolId, DateTime $from, DateTime $to): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->findEventsForSchool($schoolId, $from, $to);
    }

    /**
     * @param int $schoolId
     * @param int $sessionId
     * @return SchoolEvent[]
     * @throws Exception
     */
    public function findSessionEventsForSchool(int $schoolId, int $sessionId): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->findSessionEventsForSchool($schoolId, $sessionId);
    }

    /**
     * Finds and adds instructors to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     * @throws Exception
     */
    public function addInstructorsToEvents(array $events): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->addInstructorsToEvents($events);
    }

    /**
     * Finds and adds learning materials to a given list of user events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     * @throws Exception
     */
    public function addMaterialsToEvents(array $events): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->addMaterialsToEvents($events, $this->factory);
    }

    /**
     * Finds and adds course- and session-objectives and their competencies to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     * @throws Exception
     */
    public function addSessionDataToEvents(array $events): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->addSessionDataToEvents($events);
    }

    /**
     * @param int $id
     * @param array $events
     * @return array
     * @throws Exception
     * @see SchoolRepository::addPreAndPostRequisites()
     */
    public function addPreAndPostRequisites($id, array $events): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->addPreAndPostRequisites($id, $events);
    }

    /**
     * Get all the IDs for every school
     *
     * @return int[]
     * @throws Exception
     */
    public function getIds(): array
    {
        /** @var SchoolRepository $repository */
        $repository = $this->getRepository();
        return $repository->getIds();
    }
}
