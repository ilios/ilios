<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Offering;
use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\OfferingDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;
use function array_keys;

/**
 * Class OfferingRepository
 */
class OfferingRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, Offering::class);
    }

    protected function findIdsBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        if (array_key_exists('startDate', $criteria)) {
            $criteria['startDate'] = new DateTime($criteria['startDate']);
        }
        if (array_key_exists('endDate', $criteria)) {
            $criteria['endDate'] = new DateTime($criteria['endDate']);
        }
        if (array_key_exists('updatedAt', $criteria)) {
            $criteria['updatedAt'] = new DateTime($criteria['updatedAt']);
        }

        return $this->doFindIdsBy($criteria, $orderBy, $limit, $offset);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')->distinct()->from(Offering::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new OfferingDTO(
                $arr['id'],
                $arr['room'],
                $arr['site'],
                $arr['url'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['updatedAt']
            );
        }
        $offeringIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('x.id as offeringId, school.id as schoolId, course.id as courseId, session.id as sessionId')
            ->from(Offering::class, 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->where($qb->expr()->in('x.id', ':offeringIds'))
            ->setParameter('offeringIds', $offeringIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['offeringId']]->school = (int) $arr['schoolId'];
            $dtos[$arr['offeringId']]->course = (int) $arr['courseId'];
            $dtos[$arr['offeringId']]->session = (int) $arr['sessionId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'learnerGroups',
                'instructorGroups',
                'learners',
                'instructors',
            ],
        );

        return array_values($dtos);
    }

    public function getOfferingsForTeachingReminders(int $daysInAdvance, array $schoolIds): array
    {
        $now = time();
        $startDate = new DateTime();
        $startDate->setTimezone(new DateTimeZone('UTC'));
        $startDate->setTimestamp($now);
        $startDate->modify("midnight +{$daysInAdvance} days");

        $daysInAdvance++;
        $endDate = new DateTime();
        $endDate->setTimezone(new DateTimeZone('UTC'));
        $endDate->setTimestamp($now);
        $endDate->modify("midnight +{$daysInAdvance} days");

        $qb = $this->getEntityManager()->createQueryBuilder();
        $exp = $qb->expr();

        $qb->select('DISTINCT offering')->from(Offering::class, 'offering')
            ->join('offering.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->where($exp->andX(
                $exp->gte('offering.startDate', ':startDate'),
                $exp->lt('offering.startDate', ':endDate')
            ))
            ->andWhere($qb->expr()->eq('session.published', true))
            ->andWhere($qb->expr()->eq('course.published', true))
            ->andWhere($qb->expr()->in('school.id', ':schools'))
            ->orderBy('offering.id')
            ->setParameter(':schools', $schoolIds)
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate);

        return $qb->getQuery()->getResult();
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $related = [
            'learnerGroups',
            'instructorGroups',
            'learners',
            'instructors',
        ];
        foreach ($related as $rel) {
            if (array_key_exists($rel, $criteria)) {
                $ids = is_array($criteria[$rel]) ?
                    $criteria[$rel] : [$criteria[$rel]];
                $alias = "alias_{$rel}";
                $param = ":{$rel}";
                $qb->join("x.{$rel}", $alias);
                $qb->andWhere($qb->expr()->in("{$alias}.id", $param));
                $qb->setParameter($param, $ids);
            }
            unset($criteria[$rel]);
        }

        if (
            array_key_exists('sessions', $criteria)
            || array_key_exists('courses', $criteria)
            || array_key_exists('schools', $criteria)
        ) {
            $qb->join('x.session', 'x_session');
        }

        if (array_key_exists('courses', $criteria) || array_key_exists('schools', $criteria)) {
            $qb->leftJoin('x_session.course', 'x_course');
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->andWhere($qb->expr()->in('x_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
            unset($criteria['sessions']);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->andWhere($qb->expr()->in('x_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
            unset($criteria['courses']);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->leftJoin('x_course.school', 'x_school');
            $qb->andWhere($qb->expr()->in('x_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
            unset($criteria['schools']);
        }

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
