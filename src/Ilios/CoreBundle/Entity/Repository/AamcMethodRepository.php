<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\AamcMethodDTO;

/**
 * Class AamcMethodRepository
 */
class AamcMethodRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:AamcMethod', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('IliosCoreBundle:AamcMethod', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $aamcMethodDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $aamcMethodDTOs[$arr['id']] = new AamcMethodDTO(
                $arr['id'],
                $arr['description']
            );
        }
        $aamcMethodIds = array_keys($aamcMethodDTOs);
        $related = [
            'sessionTypes'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS aamcMethodId')->from('IliosCoreBundle:AamcMethod', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':aamcMethodIds'))
                ->orderBy('relId')
                ->setParameter('aamcMethodIds', $aamcMethodIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $aamcMethodDTOs[$arr['aamcMethodId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($aamcMethodDTOs);
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
        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->join('x.sessionTypes', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':sessionTypes'));
            $qb->setParameter(':sessionTypes', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sessionTypes']);

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
