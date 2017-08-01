<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\DTO\AuthenticationDTO;

class AuthenticationRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * Get an authentication entity by case insensitive user name.
     * @param  string $username
     * @return AuthenticationInterface|null an auth record, or NULL if none/no-unique could be found.
     */
    public function findOneByUsername($username)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'a')->from('IliosCoreBundle:Authentication', 'a');
        $qb->where($qb->expr()->like('a.username', "?1"));
        $qb->setParameter(1, '%' . $username . '%');
        $result = null;
        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            // do nothing.
        } catch (NonUniqueResultException $e) {
            // do nothing.
        }
        return $result;
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
        $qb = $this->_em->createQueryBuilder()->select('a')->distinct()->from('IliosCoreBundle:Authentication', 'a');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $authenticationDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $authenticationDTOs[$arr['person_id']] = new AuthenticationDTO(
                $arr['person_id'],
                $arr['username']
            );
        }

        return array_values($authenticationDTOs);
    }


    /**
     * Custom findBy so we can filter by related entities
     *
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
                $qb->andWhere($qb->expr()->in("a.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['user' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('a.' . $sort, $order);
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
