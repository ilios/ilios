<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AamcResourceType;
use App\Traits\FindByRepository;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\AamcResourceTypeDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;

class AamcResourceTypeRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;
    use FindByRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AamcResourceType::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(AamcResourceType::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new AamcResourceTypeDTO(
                $arr['id'],
                $arr['title'],
                $arr['description']
            );
        }
        $dtos = $this->attachRelatedToDtos(
            $dtos,
            ['terms'],
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
        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('x.terms', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['terms']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `resource_type_id`,`title`,`description`
        $entity = new AamcResourceType();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setDescription($data[2]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
