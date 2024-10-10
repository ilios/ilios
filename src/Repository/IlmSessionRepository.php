<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\IlmSession;
use App\Entity\DTO\IlmSessionDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;
use function array_values;

class IlmSessionRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, IlmSession::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(IlmSession::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new IlmSessionDTO(
                $arr['id'],
                (float) $arr['hours'],
                $arr['dueDate']
            );
        }
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, session.id AS sessionId, course.id AS courseId, school.id AS schoolId'
            )
            ->from(IlmSession::class, 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->session = (int) $arr['sessionId'];
            $dtos[$arr['xId']]->course = (int) $arr['courseId'];
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'learners',
                'learnerGroups',
                'instructors',
                'instructorGroups',
            ],
        );

        return array_values($dtos);
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $related = [
            'learners',
            'learnerGroups',
            'instructors',
            'instructorGroups',
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

        if (array_key_exists('sessions', $criteria) || array_key_exists('courses', $criteria)) {
            $qb->join('x.session', 'x_session');
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->andWhere($qb->expr()->in('x_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
            unset($criteria['sessions']);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('x_session.course', 'x_course');
            $qb->andWhere($qb->expr()->in('x_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
            unset($criteria['courses']);
        }

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
