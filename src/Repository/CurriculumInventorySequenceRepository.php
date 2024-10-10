<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CurriculumInventorySequence;
use App\Entity\DTO\CurriculumInventorySequenceDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;

class CurriculumInventorySequenceRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, CurriculumInventorySequence::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventorySequence::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CurriculumInventorySequenceDTO(
                $arr['id'],
                $arr['description']
            );
        }
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, report.id AS reportId, school.id AS schoolId'
            )
            ->from(CurriculumInventorySequence::class, 'x')
            ->join('x.report', 'report')
            ->join('report.program', 'program')
            ->join('program.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->report = (int) $arr['reportId'];
            $dtos[$arr['xId']]->school = $arr['schoolId'];
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
