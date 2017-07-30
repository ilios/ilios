<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\AlertChangeTypeDTO;

/**
 * Class AlertChangeTypeRepository
 */
class AlertChangeTypeRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:AlertChangeType', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('IliosCoreBundle:AlertChangeType', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $alertChangeTypeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $alertChangeTypeDTOs[$arr['id']] = new AlertChangeTypeDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $alertChangeTypeIds = array_keys($alertChangeTypeDTOs);
        $related = [
            'alerts'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS alertChangeTypeId')->from('IliosCoreBundle:AlertChangeType', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':alertChangeTypeIds'))
                ->orderBy('relId')
                ->setParameter('alertChangeTypeIds', $alertChangeTypeIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $alertChangeTypeDTOs[$arr['alertChangeTypeId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($alertChangeTypeDTOs);
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
        if (array_key_exists('alerts', $criteria)) {
            $ids = is_array($criteria['alerts']) ? $criteria['alerts'] : [$criteria['alerts']];
            $qb->join('x.alerts', 'al');
            $qb->andWhere($qb->expr()->in('al.id', ':alerts'));
            $qb->setParameter(':alerts', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['alerts']);

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
