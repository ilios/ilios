<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheTagger;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\AamcPcrs;
use App\Entity\DTO\AamcPcrsDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

class AamcPcrsRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, AamcPcrs::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(AamcPcrs::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new AamcPcrsDTO(
                $arr['id'],
                $arr['description']
            );
        }
        $dtos = $this->attachRelatedToDtos(
            $dtos,
            ['competencies'],
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
        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->join('x.competencies', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':competencies'));
            $qb->setParameter(':competencies', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['competencies']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `pcrs_id`,`description`
        $entity = new AamcPcrs();
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
