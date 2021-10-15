<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CurriculumInventoryExport;
use App\Entity\DTO\CurriculumInventoryExportDTO;
use App\Traits\ManagerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

use function array_keys;

class CurriculumInventoryExportRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurriculumInventoryExport::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventoryExport::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CurriculumInventoryExportDTO(
                $arr['id'],
                $arr['document'],
                $arr['createdAt'],
            );
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, user.id AS userId, report.id as reportId'
            )
            ->from(CurriculumInventoryExport::class, 'x')
            ->join('x.createdBy', 'user')
            ->join('x.report', 'report')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->createdBy = (int) $arr['userId'];
            $dtos[$arr['xId']]->report = (int) $arr['reportId'];
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
