<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\AssessmentOptionDTO;

/**
 * Class AssessmentOptionRepository
 */
class AssessmentOptionRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:AssessmentOption', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('IliosCoreBundle:AssessmentOption', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $assessmentOptionDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $assessmentOptionDTOs[$arr['id']] = new AssessmentOptionDTO(
                $arr['id'],
                $arr['name']
            );
        }
        $assessmentOptionIds = array_keys($assessmentOptionDTOs);
        $related = [
            'sessionTypes'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS assessmentOptionId')->from('IliosCoreBundle:AssessmentOption', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':assessmentOptionIds'))
                ->orderBy('relId')
                ->setParameter('assessmentOptionIds', $assessmentOptionIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $assessmentOptionDTOs[$arr['assessmentOptionId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($assessmentOptionDTOs);
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
