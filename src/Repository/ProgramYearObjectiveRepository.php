<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProgramYearObjective;
use App\Entity\DTO\ProgramYearObjectiveDTO;
use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Symfony\Contracts\Cache\CacheInterface;

use function array_values;
use function array_keys;

class ProgramYearObjectiveRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, ProgramYearObjective::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(ProgramYearObjective::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new ProgramYearObjectiveDTO(
                $arr['id'],
                $arr['title'],
                $arr['position'],
                $arr['active']
            );
        }
        $programYearObjectiveIds = array_keys($dtos);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id AS xId, ' .
                'programYear.id AS programYearId, programYear.locked AS programYearIsLocked, ' .
                'programYear.archived AS programYearIsArchived'
            )
            ->from(ProgramYearObjective::class, 'x')
            ->join('x.programYear', 'programYear')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $programYearObjectiveIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->programYearIsLocked = (bool) $arr['programYearIsLocked'];
            $dtos[$arr['xId']]->programYearIsArchived = (bool) $arr['programYearIsArchived'];
            $dtos[$arr['xId']]->programYear = (int) $arr['programYearId'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as id, c.id as competencyId, a.id as ancestorId')
            ->from(ProgramYearObjective::class, 'x')
            ->leftJoin('x.competency', 'c')
            ->leftJoin('x.ancestor', 'a')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $programYearObjectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['id']]->competency = $arr['competencyId'] ? (int)$arr['competencyId'] : null;
            $dtos[$arr['id']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'terms',
                'meshDescriptors',
                'courseObjectives',
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
        if (array_key_exists('terms', $criteria)) {
            if (is_array($criteria['terms'])) {
                $ids = $criteria['terms'];
            } else {
                $ids = [$criteria['terms']];
            }
            $qb->join('x.terms', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['terms']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalObjectiveCount(): int
    {
        return (int) $this->_em->createQuery('SELECT COUNT(o.id) FROM App\Entity\ProgramYearObjective o')
            ->getSingleScalarResult();
    }
}
