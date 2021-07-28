<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CourseClerkshipType;
use App\Entity\DTO\CourseClerkshipTypeDTO;
use Doctrine\Persistence\ManagerRegistry;

class CourseClerkshipTypeRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseClerkshipType::class);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\CourseClerkshipType', 'x');

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
        $qb = $this->_em->createQueryBuilder()
            ->select('x')->distinct()
            ->from('App\Entity\CourseClerkshipType', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $courseClerkshipTypeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $courseClerkshipTypeDTOs[$arr['id']] = new CourseClerkshipTypeDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $courseClerkshipTypeIds = array_keys($courseClerkshipTypeDTOs);
        $related = [
            'courses'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS courseClerkshipTypeId')
                ->from('App\Entity\CourseClerkshipType', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':courseClerkshipTypeIds'))
                ->orderBy('relId')
                ->setParameter('courseClerkshipTypeIds', $courseClerkshipTypeIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $courseClerkshipTypeDTOs[$arr['courseClerkshipTypeId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($courseClerkshipTypeDTOs);
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
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.courses', 'c');
            $qb->andWhere($qb->expr()->in('c.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);

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

    public function import(array $data, string $type): void
    {
        // `course_clerkship_type_id`,`title`
        $entity = new CourseClerkshipType();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $this->update($entity, true);
    }
}
