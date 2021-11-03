<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CourseLearningMaterial;
use App\Entity\DTO\CourseLearningMaterialDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;
use function array_values;

class CourseLearningMaterialRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseLearningMaterial::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CourseLearningMaterial::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var CourseLearningMaterialDTO[] $dtos */
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CourseLearningMaterialDTO(
                $arr['id'],
                $arr['notes'],
                $arr['required'],
                $arr['publicNotes'],
                $arr['position'],
                $arr['startDate'],
                $arr['endDate']
            );
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, learningMaterial.id AS learningMaterialId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'status.id as statusId, school.id AS schoolId'
            )
            ->from(CourseLearningMaterial::class, 'x')
            ->join('x.course', 'course')
            ->join('course.school', 'school')
            ->join('x.learningMaterial', 'learningMaterial')
            ->leftJoin('learningMaterial.status', 'status')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->course = (int) $arr['courseId'];
            $dtos[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $dtos[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
            $dtos[$arr['xId']]->learningMaterial = (int) $arr['learningMaterialId'];
            $dtos[$arr['xId']]->status = (int) $arr['statusId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'meshDescriptors'
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
        if (array_key_exists('meshDescriptors', $criteria)) {
            if (is_array($criteria['meshDescriptors'])) {
                $ids = $criteria['meshDescriptors'];
            } else {
                $ids = [$criteria['meshDescriptors']];
            }
            $qb->join('x.meshDescriptors', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':meshDescriptors'));
            $qb->setParameter(':meshDescriptors', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['meshDescriptors']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function getTotalCourseLearningMaterialCount(): int
    {
        return (int) $this->_em->createQuery('SELECT COUNT(l.id) FROM App\Entity\CourseLearningMaterial l')
            ->getSingleScalarResult();
    }
}
