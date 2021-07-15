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
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from(CurriculumInventoryExport::class, 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventoryExport::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var CurriculumInventoryExportDTO[] $curriculumInventoryExportDTOs */
        $curriculumInventoryExportDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $curriculumInventoryExportDTOs[$arr['id']] = new CurriculumInventoryExportDTO(
                $arr['id'],
                $arr['document'],
                $arr['createdAt'],
            );
        }

        $curriculumInventoryExportIds = array_keys($curriculumInventoryExportDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, user.id AS userId'
            )
            ->from(CurriculumInventoryExport::class, 'x')
            ->join('x.createdBy', 'user')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryExportIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $curriculumInventoryExportDTOs[$arr['xId']]->createdBy = (int) $arr['userId'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, report.id AS reportId'
            )
            ->from(CurriculumInventoryExport::class, 'x')
            ->join('x.report', 'report')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryExportIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $curriculumInventoryExportDTOs[$arr['xId']]->report = (int) $arr['reportId'];
        }

        return array_values($curriculumInventoryExportDTOs);
    }


    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}
