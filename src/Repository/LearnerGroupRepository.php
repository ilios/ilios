<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\FindByRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\LearnerGroup;
use App\Entity\DTO\LearnerGroupDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;
use function array_values;

class LearnerGroupRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;
    use FindByRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LearnerGroup::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(LearnerGroup::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new LearnerGroupDTO(
                $arr['id'],
                $arr['title'],
                $arr['location'],
                $arr['url'],
                $arr['needsAccommodation']
            );
        }
        $qb = $this->_em->createQueryBuilder()
            ->select('l.id as learnerGroupId, plg.id as parentId, c.id as cohortId, alg.id as ancestorId')
            ->from(LearnerGroup::class, 'l')
            ->join('l.cohort', 'c')
            ->leftJoin('l.parent', 'plg')
            ->leftJoin('l.ancestor', 'alg')
            ->where($qb->expr()->in('l.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['learnerGroupId']]->cohort = (int) $arr['cohortId'];
            $dtos[$arr['learnerGroupId']]->parent = $arr['parentId'] ? (int)$arr['parentId'] : null;
            $dtos[$arr['learnerGroupId']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'children',
                'ilmSessions',
                'offerings',
                'instructorGroups',
                'users',
                'instructors',
                'descendants',
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
        if (array_key_exists('cohort', $criteria)) {
            $criteria['cohorts'][] = $criteria['cohort'];
            unset($criteria['cohort']);
        }
        if (array_key_exists('cohorts', $criteria)) {
            $ids = is_array($criteria['cohorts']) ? $criteria['cohorts'] : [$criteria['cohorts']];
            $qb->join('x.cohort', 'l_cohort');
            $qb->andWhere($qb->expr()->in('l_cohort.id', ':cohorts'));
            $qb->setParameter(':cohorts', $ids);
        }

        if (array_key_exists('parent', $criteria)) {
            $criteria['parents'][] = $criteria['parent'];
            unset($criteria['parent']);
        }
        if (array_key_exists('parents', $criteria)) {
            $ids = is_array($criteria['parents'])
                ? $criteria['parents'] : [$criteria['parents']];
            if (in_array(null, $ids)) {
                $ids = array_diff($ids, [null]);
                $qb->andWhere('x.parent IS NULL');
            }
            if ($ids !== []) {
                $qb->join('x.parent', 'l_parent');
                $qb->andWhere($qb->expr()->in('l_parent.id', ':parents'));
                $qb->setParameter(':parents', $ids);
            }
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('l.offerings', 't_offering');
            $qb->leftJoin('t_offering.session', 't_session1');
            $qb->leftJoin('t_session1.terms', 't_term1');
            $qb->leftJoin('t_session1.course', 't_course1');
            $qb->leftJoin('t_course1.terms', 't_term2');
            $qb->leftJoin('l.ilmSessions', 't_ilm');
            $qb->leftJoin('t_ilm.session', 't_session2');
            $qb->leftJoin('t_session2.terms', 't_term3');
            $qb->leftJoin('t_session2.course', 't_course2');
            $qb->leftJoin('t_course2.terms', 't_term4');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_term1.id', ':terms'),
                    $qb->expr()->in('t_term2.id', ':terms'),
                    $qb->expr()->in('t_term3.id', ':terms'),
                    $qb->expr()->in('t_term4.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['cohorts']);
        unset($criteria['parents']);
        unset($criteria['terms']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
