<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;
use function array_keys;

class CurriculumInventorySequenceBlockRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, CurriculumInventorySequenceBlock::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventorySequenceBlock::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CurriculumInventorySequenceBlockDTO(
                $arr['id'],
                $arr['title'],
                $arr['description'],
                $arr['required'],
                $arr['childSequenceOrder'],
                $arr['orderInSequence'],
                $arr['minimum'],
                $arr['maximum'],
                $arr['track'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['duration']
            );
        }
        $curriculumInventorySequenceBlockIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, report.id AS reportId, school.id AS schoolId, ' .
                'startingAcademicLevel.id AS startingAcademicLevelId, ' .
                'endingAcademicLevel.id AS endingAcademicLevelId, course.id AS courseId, parent.id AS parentId '
            )
            ->from(CurriculumInventorySequenceBlock::class, 'x')
            ->join('x.report', 'report')
            ->join('report.program', 'program')
            ->join('program.school', 'school')
            ->join('x.startingAcademicLevel', 'startingAcademicLevel')
            ->join('x.endingAcademicLevel', 'endingAcademicLevel')
            ->leftJoin('x.parent', 'parent')
            ->leftJoin('x.course', 'course')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventorySequenceBlockIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->report = (int) $arr['reportId'];
            $dtos[$arr['xId']]->startingAcademicLevel =
                $arr['startingAcademicLevelId'] ? (int)$arr['startingAcademicLevelId'] : null;
            $dtos[$arr['xId']]->endingAcademicLevel =
                $arr['endingAcademicLevelId'] ? (int)$arr['endingAcademicLevelId'] : null;
            $dtos[$arr['xId']]->course = $arr['courseId'] ? (int)$arr['courseId'] : null;
            $dtos[$arr['xId']]->parent = $arr['parentId'] ? (int)$arr['parentId'] : null;
            $dtos[$arr['xId']]->school = $arr['schoolId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'children',
                'sessions',
                'excludedSessions',
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
        if (array_key_exists('children', $criteria)) {
            $ids = is_array($criteria['children']) ? $criteria['children'] : [$criteria['children']];
            $qb->join('x.children', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':children'));
            $qb->setParameter(':children', $ids);
        }
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('x.sessions', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['children']);
        unset($criteria['sessions']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
