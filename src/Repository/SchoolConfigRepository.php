<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\FindByRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\SchoolConfig;
use App\Entity\DTO\SchoolConfigDTO;
use Doctrine\Persistence\ManagerRegistry;

class SchoolConfigRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;
    use FindByRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolConfig::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(SchoolConfig::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new SchoolConfigDTO(
                $arr['id'],
                $arr['name'],
                $arr['value']
            );
        }
        $schoolConfigIds = array_keys($dtos);

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as xId, school.id AS schoolId')
            ->from('App\Entity\SchoolConfig', 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $schoolConfigIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        return array_values($dtos);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getValue($name)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('x.value')->from('App\Entity\SchoolConfig', 'x')
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
