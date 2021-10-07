<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\FindByRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\IngestionException;
use App\Entity\DTO\IngestionExceptionDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;

class IngestionExceptionRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;
    use FindByRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IngestionException::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(IngestionException::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new IngestionExceptionDTO(
                $arr['id'],
                $arr['uid']
            );
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, user.id AS userId'
            )
            ->from('App\Entity\IngestionException', 'x')
            ->join('x.user', 'user')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->user = (int) $arr['userId'];
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
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
