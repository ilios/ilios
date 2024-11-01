<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\SchoolConfig;
use App\Entity\DTO\SchoolConfigDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;
use function array_values;

class SchoolConfigRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, SchoolConfig::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')->distinct()->from(SchoolConfig::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new SchoolConfigDTO(
                $arr['id'],
                $arr['name'],
                $arr['value']
            );
        }
        $schoolConfigIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('x.id as xId, school.id AS schoolId')
            ->from(SchoolConfig::class, 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $schoolConfigIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        return array_values($dtos);
    }

    public function getValue(string $name): mixed
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('x.value')->from(SchoolConfig::class, 'x')
            ->where($qb->expr()->eq('x.name', ':name'))
            ->setParameter('name', $name);

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            $result = null;
        }

        return $result;
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
