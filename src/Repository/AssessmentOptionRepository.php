<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AssessmentOption;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\AssessmentOptionDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;

class AssessmentOptionRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssessmentOption::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(AssessmentOption::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new AssessmentOptionDTO(
                $arr['id'],
                $arr['name']
            );
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            ['sessionTypes'],
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
        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->join('x.sessionTypes', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':sessionTypes'));
            $qb->setParameter(':sessionTypes', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sessionTypes']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `assessment_option_id`,`name`
        $entity = new AssessmentOption();
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
