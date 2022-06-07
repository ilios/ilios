<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CurriculumInventoryExport;
use App\Entity\DTO\CurriculumInventoryExportDTO;
use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Cache\CacheInterface;

use function array_keys;

class CurriculumInventoryExportRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, CurriculumInventoryExport::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventoryExport::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

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
