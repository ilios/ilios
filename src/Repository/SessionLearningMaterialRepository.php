<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\SessionLearningMaterial;
use App\Entity\DTO\SessionLearningMaterialDTO;
use Doctrine\Persistence\ManagerRegistry;

class SessionLearningMaterialRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionLearningMaterial::class);
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\SessionLearningMaterial', 'x');

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
            ->distinct()->from('App\Entity\SessionLearningMaterial', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var SessionLearningMaterialDTO[] $sessionLearningMaterialDTOs */
        $sessionLearningMaterialDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessionLearningMaterialDTOs[$arr['id']] = new SessionLearningMaterialDTO(
                $arr['id'],
                $arr['notes'],
                $arr['required'],
                $arr['publicNotes'],
                $arr['position'],
                $arr['startDate'],
                $arr['endDate']
            );
        }
        $sessionLearningMaterialIds = array_keys($sessionLearningMaterialDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, learningMaterial.id AS learningMaterialId, session.id AS sessionId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'status.id as statusId, school.id AS schoolId'
            )
            ->from('App\Entity\SessionLearningMaterial', 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->join('x.learningMaterial', 'learningMaterial')
            ->leftJoin('learningMaterial.status', 'status')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionLearningMaterialIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionLearningMaterialDTOs[$arr['xId']]->session = (int) $arr['sessionId'];
            $sessionLearningMaterialDTOs[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $sessionLearningMaterialDTOs[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $sessionLearningMaterialDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $sessionLearningMaterialDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
            $sessionLearningMaterialDTOs[$arr['xId']]->learningMaterial = (int) $arr['learningMaterialId'];
            $sessionLearningMaterialDTOs[$arr['xId']]->status = (int) $arr['statusId'];
        }

        $related = [
            'meshDescriptors'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS sessionLearningMaterialId')
                ->from('App\Entity\SessionLearningMaterial', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $sessionLearningMaterialIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $sessionLearningMaterialDTOs[$arr['sessionLearningMaterialId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($sessionLearningMaterialDTOs);
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

        if (count($criteria)) {
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

    /**
     * @return int
     */
    public function getTotalSessionLearningMaterialCount()
    {
        return $this->_em->createQuery('SELECT COUNT(l.id) FROM App\Entity\SessionLearningMaterial l')
            ->getSingleScalarResult();
    }
}
