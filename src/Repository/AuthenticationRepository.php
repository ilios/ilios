<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Authentication;
use App\Service\DTOCacheManager;
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

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, Authentication::class);
    }

    /**
     * Get an authentication entity by case insensitive user name.
     */
    public function findOneByUsername(string $username): ?AuthenticationInterface
    {
        $username = strtolower($username);
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')->from(Authentication::class, 'a');
        $qb->where($qb->expr()->eq(
            $qb->expr()->lower('a.username'),
            ":username"
        ));
        $qb->setParameter('username', trim(strtolower($username)));
        $result = null;
        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException) {
            // do nothing.
        } catch (NonUniqueResultException) {
            // do nothing.
        }
        return $result;
    }

    /**
     * Special case for Authentication since the ID is the user
     */
    protected function findIdsBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $keys = array_keys($criteria);

        //if the only criteria is the IDs we don't need to look that up
        if ($keys === ['user'] && is_null($orderBy) && is_null($limit) && is_null($offset)) {
            return is_array($criteria['user']) ? $criteria['user'] : [$criteria['user']];
        }
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select("x")
            ->from(Authentication::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $results  = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_column($results, 'person_id');
    }

    /**
     * Get all the usernames
     */
    public function getUsernames(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->addSelect('a.username')->from(Authentication::class, 'a');

        return array_map(fn(array $arr) => $arr['username'], $qb->getQuery()->getScalarResult());
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('x')
            ->distinct()
            ->from(Authentication::class, 'x');
        $qb->where($qb->expr()->in('x.user', ':ids'));
        $qb->setParameter(':ids', $ids);

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
