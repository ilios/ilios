<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\FindByRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\Report;
use App\Entity\DTO\ReportDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;

class ReportRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;
    use FindByRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(Report::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new ReportDTO(
                $arr['id'],
                $arr['title'],
                $arr['createdAt'],
                $arr['subject'],
                $arr['prepositionalObject'],
                $arr['prepositionalObjectTableRowId']
            );
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, school.id AS schoolId, user.id AS userId'
            )
            ->from('App\Entity\Report', 'x')
            ->join('x.user', 'user')
            ->leftJoin('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->school = $arr['schoolId'] ? (int) $arr['schoolId'] : null;
            $dtos[$arr['xId']]->user = (int) $arr['userId'];
        }

        return array_values($dtos);
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
