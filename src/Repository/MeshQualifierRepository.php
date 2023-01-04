<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\MeshQualifier;
use App\Entity\DTO\MeshQualifierDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;

class MeshQualifierRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, MeshQualifier::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(MeshQualifier::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new MeshQualifierDTO(
                $arr['id'],
                $arr['name'],
                $arr['createdAt'],
                $arr['updatedAt']
            );
        }


        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'descriptors',
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
        $related = [
            'descriptors',
        ];
        foreach ($related as $rel) {
            if (array_key_exists($rel, $criteria)) {
                $ids = is_array($criteria[$rel]) ?
                    $criteria[$rel] : [$criteria[$rel]];
                $alias = "alias_{$rel}";
                $param = ":{$rel}";
                $qb->join("x.{$rel}", $alias);
                $qb->andWhere($qb->expr()->in("{$alias}.id", $param));
                $qb->setParameter($param, $ids);
            }
            unset($criteria[$rel]);
        }

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
