<?php

declare(strict_types=1);

namespace App\Traits;

use App\Service\DTOCacheTagger;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Trait ManagerRepository
 * Applied to Entity repositories to give them generic repository functionality
 * Relies on the repository extending Doctrine\ORM\EntityRepository
 * and implementing App\Repository\DTORepositoryInterface
 */
trait ManagerRepository
{
    abstract protected function getEntityName();
    abstract protected function getEntityManager();
    abstract public function find($id);
    abstract protected function hydrateDTOsFromIds(array $ids): array;

    public function getClass(): string
    {
        return $this->getEntityName();
    }

    public function flushAndClear(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById($id): ?object
    {
        return $this->find($id);
    }

    public function findDTOBy(array $criteria): ?object
    {
        $results = $this->findDTOsBy($criteria, null, 1);
        return $results[0] ?? null;
    }

    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $ids = $this->findIdsBy($criteria, $orderBy, $limit, $offset);
        $cachedDtos = $this->getCachedDTOs($ids);
        $idField = $this->getIdField();
        $cachedIds = array_column($cachedDtos, $idField);

        $missedDtos = [];
        $missedIds = array_diff($ids, $cachedIds);
        if (count($missedIds)) {
            $missedDtos = $this->hydrateDTOsFromIds($missedIds);
            foreach ($missedDtos as $dto) {
                $tagger = $this->getCacheTagger();
                $this->cache->get(
                    $this->getDtoCacheKey($dto->$idField),
                    function (ItemInterface $item) use ($dto, $tagger) {
                        $tagger->tag($item, $dto);
                        return $dto;
                    }
                );
            }
        }

        $dtos = array_values([...$cachedDtos, ...$missedDtos]);

        // Lots of work here to re-order the results in the same way as originally requested
        $dtosById = [];
        foreach ($dtos as $dto) {
            $dtosById[$dto->$idField] = $dto;
        }
        $rhett = [];
        foreach ($ids as $id) {
            $rhett[] = $dtosById[$id];
        }

        return $rhett;
    }

    protected function findIdsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->doFindIdsBy($criteria, $orderBy, $limit, $offset);
    }

    protected function doFindIdsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $idField = $this->getIdField();
        $keys = array_keys($criteria);

        //if the only criteria is the IDs we don't need to look that up
        if ($keys === [$idField] && is_null($orderBy) && is_null($limit) && is_null($offset)) {
            return $criteria[$idField];
        }
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select("x.${idField} as XID")
            ->distinct()
            ->from($this->getEntityName(), 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }

    protected function getDtoCacheKey(mixed $id): string
    {
        return md5(self::class . 'DTO' . $id);
    }

    /**
     * Look into the cache and find any DTOs we already have
     * But don't persist anything to the cache here, we'll do that after
     * fetching missing items.
     */
    protected function getCachedDTOs(array $ids): array
    {
        $dtosOrMisses = array_map(
            fn(mixed $id) => $this->cache->get(
                $this->getDtoCacheKey($id),
                function (ItemInterface $item, bool &$save) {
                    $save = false;
                    return false;
                }
            ),
            $ids
        );

        return array_filter($dtosOrMisses);
    }

    public function update($entity, $andFlush = true, $forceId = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($forceId) {
            $metadata = $this->getEntityManager()->getClassMetaData($entity::class);
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete($entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function create(): object
    {
        $class = $this->getEntityName();
        return new $class();
    }

    public function getIdField(): string
    {
        $meta = $this->getEntityManager()->getClassMetadata($this->getEntityName());
        return $meta->getSingleIdentifierFieldName();
    }

    public function isEntityPersisted(object $entity): bool
    {
        return $this->getEntityManager()->contains($entity);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('DISTINCT x')->from($this->getEntityName(), 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    protected function attachRelatedToDtos(
        array $dtos,
        array $related,
    ): array {
        if ($dtos === []) {
            return $dtos;
        }
        $ids = array_keys($dtos);
        $relatedMetadata = array_filter(
            $this->getClassMetadata()->associationMappings,
            fn (array $arr) => array_key_exists('joinTable', $arr) && in_array($arr['fieldName'], $related)
        );
        $owningSideSets = $this->extractSetsFromOwningSideMetadata(
            array_filter($relatedMetadata, fn(array $arr) => $arr['isOwningSide'])
        );
        $inverseSideSets = $this->extractSetsFromInverseSideMetadata(
            array_filter($relatedMetadata, fn(array $arr) => !$arr['isOwningSide'])
        );
        $remainingRelated = array_diff($related, array_keys($owningSideSets), array_keys($inverseSideSets));
        $sets = [
            ...array_values($owningSideSets),
            ...array_values($inverseSideSets),
        ];

        $dtos = $this->attachManySetsToDtos($dtos, $sets);

        foreach ($remainingRelated as $rel) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('r.id AS relId, x.id AS xId')->from($this->getEntityName(), 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $ids);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $dtos[$arr['xId']]->{$rel}[] = $arr['relId'];
            }
        }

        return $dtos;
    }

    protected function attachManySetsToDtos(array $dtos, array $sets): array
    {
        $ids = array_keys($dtos);
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        foreach ($sets as $arr) {
            $qb = $conn->createQueryBuilder();
            $qb->select($arr['relatedIdColumn'], $arr['dtoIdColumn'])
                ->from($arr['tableName'])
                ->where("${arr['dtoIdColumn']} IN(:ids)")
                ->setParameter('ids', $ids, Connection::PARAM_STR_ARRAY);
            $result = $qb->executeQuery()->fetchAllAssociative();
            foreach ($result as $row) {
                $dtos[$row[$arr['dtoIdColumn']]]->{$arr['fieldName']}[] = $row[$arr['relatedIdColumn']];
            }
        }

        return $dtos;
    }

    protected function extractSetsFromOwningSideMetadata(array $arr): array
    {
        return array_map(function (array $arr) {
            return [
                'fieldName' => $arr['fieldName'],
                'tableName'  => $arr['joinTable']['name'],
                'dtoIdColumn'  => $arr['joinTable']['joinColumns'][0]['name'],
                'relatedIdColumn'  => $arr['joinTable']['inverseJoinColumns'][0]['name'],
            ];
        }, $arr);
    }

    protected function extractSetsFromInverseSideMetadata(array $arr): array
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        return array_map(function ($rel) use ($em) {
            $metadata = $em->getClassMetadata($rel['targetEntity']);
            $mapping = $metadata->associationMappings[$rel['mappedBy']];
            return [
                'fieldName' => $rel['fieldName'],
                'tableName'  => $mapping['joinTable']['name'],
                'dtoIdColumn'  => $mapping['joinTable']['inverseJoinColumns'][0]['name'],
                'relatedIdColumn'  => $mapping['joinTable']['joinColumns'][0]['name'],
            ];
        }, $arr);
    }

    protected function attachClosingCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }
    }

    abstract protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void;

    protected function getCache(): TagAwareCacheInterface
    {
        if (!isset($this->cache)) {
            throw new Exception("The 'cache' property is missing from " . self::class);
        }

        return $this->cache;
    }

    protected function getCacheTagger(): DTOCacheTagger
    {
        if (!isset($this->cacheTagger)) {
            throw new Exception("The 'cacheTagger' property is missing from " . self::class);
        }

        return $this->cacheTagger;
    }
}
