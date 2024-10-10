<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\SessionLearningMaterial;
use App\Entity\DTO\SessionLearningMaterialDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;
use function array_keys;

class SessionLearningMaterialRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, SessionLearningMaterial::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(SessionLearningMaterial::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new SessionLearningMaterialDTO(
                $arr['id'],
                $arr['notes'],
                $arr['required'],
                $arr['publicNotes'],
                $arr['position'],
                $arr['startDate'],
                $arr['endDate']
            );
        }
        $sessionLearningMaterialIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, learningMaterial.id AS learningMaterialId, session.id AS sessionId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'status.id as statusId, school.id AS schoolId'
            )
            ->from(SessionLearningMaterial::class, 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->join('x.learningMaterial', 'learningMaterial')
            ->leftJoin('learningMaterial.status', 'status')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionLearningMaterialIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->session = (int) $arr['sessionId'];
            $dtos[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $dtos[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $dtos[$arr['xId']]->course = (int) $arr['courseId'];
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
            $dtos[$arr['xId']]->learningMaterial = (int) $arr['learningMaterialId'];
            $dtos[$arr['xId']]->status = (int) $arr['statusId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'meshDescriptors',
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

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.session', 'session');
            $qb->join('session.course', 'course');
            $qb->join('course.school', 'school');
            $qb->andWhere($qb->expr()->in('school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['meshDescriptors']);
        unset($criteria['schools']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function getTotalSessionLearningMaterialCount(): int
    {
        return (int) $this->getEntityManager()
            ->createQuery('SELECT COUNT(l.id) FROM App\Entity\SessionLearningMaterial l')
            ->getSingleScalarResult();
    }
}
