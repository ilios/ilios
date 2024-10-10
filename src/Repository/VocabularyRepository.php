<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DefaultDataImporter;
use App\Service\DTOCacheManager;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\Vocabulary;
use App\Entity\DTO\VocabularyDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;
use function array_keys;

class VocabularyRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, Vocabulary::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(Vocabulary::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new VocabularyDTO(
                $arr['id'],
                $arr['title'],
                $arr['active']
            );
        }
        $vocabularyIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, school.id AS schoolId'
            )
            ->from(Vocabulary::class, 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $vocabularyIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'terms',
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
            'terms',
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

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `vocabulary_id`,`title`,`school_id`, `active`
        $entity = new Vocabulary();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($referenceMap[DefaultDataImporter::SCHOOL . $data[2]]);
        $entity->setActive($data[3]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
