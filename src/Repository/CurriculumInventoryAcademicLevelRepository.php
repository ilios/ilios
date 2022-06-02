<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;
use function array_keys;

class CurriculumInventoryAcademicLevelRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurriculumInventoryAcademicLevel::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventoryAcademicLevel::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CurriculumInventoryAcademicLevelDTO(
                $arr['id'],
                $arr['name'],
                $arr['description'],
                $arr['level']
            );
        }
        $curriculumInventoryAcademicLevelIds = array_keys($dtos);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, report.id AS reportId, school.id AS schoolId'
            )
            ->from(CurriculumInventoryAcademicLevel::class, 'x')
            ->join('x.report', 'report')
            ->join('report.program', 'program')
            ->join('program.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryAcademicLevelIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->report = (int) $arr['reportId'];
            $dtos[$arr['xId']]->school = $arr['schoolId'] ? (int)$arr['schoolId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            ['startingSequenceBlocks', 'endingSequenceBlocks'],
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
        if (array_key_exists('startingSequenceBlocks', $criteria)) {
            $ids = is_array($criteria['startingSequenceBlocks'])
                ? $criteria['startingSequenceBlocks'] : [$criteria['startingSequenceBlocks']];
            $qb->join('x.startingSequenceBlocks', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':startingSequenceBlocks'));
            $qb->setParameter(':startingSequenceBlocks', $ids);
        }

        if (array_key_exists('endingSequenceBlocks', $criteria)) {
            $ids = is_array($criteria['endingSequenceBlocks'])
                ? $criteria['endingSequenceBlocks'] : [$criteria['endingSequenceBlocks']];
            $qb->join('x.endingSequenceBlocks', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':endingSequenceBlocks'));
            $qb->setParameter(':endingSequenceBlocks', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['startingSequenceBlocks']);
        unset($criteria['endingSequenceBlocks']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
