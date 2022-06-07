<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheTagger;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CourseClerkshipType;
use App\Entity\DTO\CourseClerkshipTypeDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

use function array_values;

class CourseClerkshipTypeRepository extends ServiceEntityRepository implements
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
        parent::__construct($registry, CourseClerkshipType::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('x')->distinct()
            ->from(CourseClerkshipType::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CourseClerkshipTypeDTO(
                $arr['id'],
                $arr['title']
            );
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'courses'
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
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.courses', 'c');
            $qb->andWhere($qb->expr()->in('c.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `course_clerkship_type_id`,`title`
        $entity = new CourseClerkshipType();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
