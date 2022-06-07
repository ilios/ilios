<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Alert;
use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\AlertDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

use function array_values;

class AlertRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, Alert::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(Alert::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new AlertDTO(
                $arr['id'],
                $arr['tableRowId'],
                $arr['tableName'],
                $arr['additionalText'],
                $arr['dispatched']
            );
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'changeTypes',
                'instigators',
                'recipients'
            ],
        );

        return array_values($dtos);
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if (array_key_exists('changeTypes', $criteria)) {
            $ids = is_array($criteria['changeTypes']) ?
                $criteria['changeTypes'] : [$criteria['changeTypes']];
            $qb->join('x.changeTypes', 'act');
            $qb->andWhere($qb->expr()->in('act.id', ':changeTypes'));
            $qb->setParameter(':changeTypes', $ids);
        }
        if (array_key_exists('instigators', $criteria)) {
            $ids = is_array($criteria['instigators']) ?
                $criteria['instigators'] : [$criteria['instigators']];
            $qb->join('x.instigators', 'ins');
            $qb->andWhere($qb->expr()->in('ins.id', ':instigators'));
            $qb->setParameter(':instigators', $ids);
        }
        if (array_key_exists('recipients', $criteria)) {
            $ids = is_array($criteria['recipients']) ?
                $criteria['recipients'] : [$criteria['recipients']];
            $qb->join('x.recipients', 'rcp');
            $qb->andWhere($qb->expr()->in('rcp.id', ':recipients'));
            $qb->setParameter(':recipients', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['changeTypes']);
        unset($criteria['instigators']);
        unset($criteria['recipients']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
