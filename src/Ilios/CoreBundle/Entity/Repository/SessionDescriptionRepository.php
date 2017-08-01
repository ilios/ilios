<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\SessionDescriptionDTO;

/**
 * Class SessionDescriptionRepository
 */
class SessionDescriptionRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:SessionDescription', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('IliosCoreBundle:SessionDescription', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var SessionDescriptionDTO[] $sessionDescriptionDTOs */
        $sessionDescriptionDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessionDescriptionDTOs[$arr['id']] = new SessionDescriptionDTO(
                $arr['id'],
                $arr['description']
            );
        }
        $sessionDescriptionIds = array_keys($sessionDescriptionDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, session.id AS sessionId, course.id AS courseId, school.id AS schoolId'
            )
            ->from('IliosCoreBundle:SessionDescription', 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionDescriptionIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionDescriptionDTOs[$arr['xId']]->session = (int) $arr['sessionId'];
            $sessionDescriptionDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $sessionDescriptionDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }
        
        return array_values($sessionDescriptionDTOs);
    }

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
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
                $qb->addOrderBy('x.'.$sort, $order);
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
