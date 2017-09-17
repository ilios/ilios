<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\MeshTermDTO;

/**
 * Class MeshTermRepository
 */
class MeshTermRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:MeshTerm', 'x');

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
            ->distinct()->from('IliosCoreBundle:MeshTerm', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var MeshTermDTO[] $meshTermDTOs */
        $meshTermDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $meshTermDTOs[$arr['id']] = new MeshTermDTO(
                $arr['id'],
                $arr['meshTermUid'],
                $arr['name'],
                $arr['lexicalTag'],
                $arr['conceptPreferred'],
                $arr['recordPreferred'],
                $arr['permuted'],
                $arr['createdAt'],
                $arr['updatedAt']
            );
        }
        $meshTermIds = array_keys($meshTermDTOs);

        $related = [
            'concepts',
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS meshTermId')
                ->from('IliosCoreBundle:MeshTerm', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $meshTermIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $meshTermDTOs[$arr['meshTermId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($meshTermDTOs);
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
        $related = [
            'concepts',
        ];
        foreach ($related as $rel) {
            if (array_key_exists($rel, $criteria)) {
                $ids = is_array($criteria[$rel]) ?
                    $criteria[$rel] : [$criteria[$rel]];
                $alias = "alias_${rel}";
                $param = ":${rel}";
                $qb->join("x.${rel}", $alias);
                $qb->andWhere($qb->expr()->in("${alias}.id", $param));
                $qb->setParameter($param, $ids);
            }
            unset($criteria[$rel]);
        }

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
