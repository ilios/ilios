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

use function is_array;

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
     */
    public function findOneByUsername($username): ?AuthenticationInterface
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
     */
    public function getUsernames(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect('a.username')->from(Authentication::class, 'a');

        return array_map(fn(array $arr) => $arr['username'], $qb->getQuery()->getScalarResult());
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(Authentication::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['person_id']] = new AuthenticationDTO(
                $arr['person_id'],
                $arr['username']
            );
        }

        return array_values($dtos);
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['user' => 'ASC'];
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
    }
}
