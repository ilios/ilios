<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Authentication;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\AuthenticationInterface;
use App\Entity\DTO\AuthenticationDTO;
use Doctrine\Persistence\ManagerRegistry;

class AuthenticationRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Authentication::class);
    }

    /**
     * Get an authentication entity by case insensitive user name.
     * @param  string $username
     * @return AuthenticationInterface|null an auth record, or NULL if none/no-unique could be found.
     */
    public function findOneByUsername($username)
    {
        $username = strtolower($username);
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')->from(Authentication::class, 'a');
        $qb->where($qb->expr()->eq(
            $qb->expr()->lower('a.username'),
            ":username"
        ));
        $qb->setParameter('username', trim(strtolower($username)));
        $result = null;
        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            // do nothing.
        } catch (NonUniqueResultException) {
            // do nothing.
        }
        return $result;
    }

    /**
     * Get all the usernames
     *
     * @return string[]
     */
    public function getUsernames()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect('a.username')->from(Authentication::class, 'a');

        return array_map(fn(array $arr) => $arr['username'], $qb->getQuery()->getScalarResult());
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
        $qb = $this->_em->createQueryBuilder()->select('a')->distinct()->from('App\Entity\Authentication', 'a');
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
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if ($criteria !== []) {
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
