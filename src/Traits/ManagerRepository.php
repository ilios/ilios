<?php

declare(strict_types=1);

namespace App\Traits;

use App\Service\DTOCacheManager;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ManyToManyAssociationMapping;
use Doctrine\ORM\Mapping\ManyToManyInverseSideMapping;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Trait ManagerRepository
 * Applied to Entity repositories to give them generic repository functionality
 * Relies on the repository extending Doctrine\ORM\EntityRepository
 * and implementing App\Repository\DTORepositoryInterface
 */
trait ManagerRepository
{
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
    abstract protected function getEntityName();
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
    abstract protected function getEntityManager();
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint,SlevomatCodingStandard.TypeHints.ParameterTypeHint
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

    public function findOneById(string|int $id): ?object
    {
        return $this->find($id);
    }

    public function findDTOBy(array $criteria): ?object
    {
        $results = $this->findDTOsBy($criteria, null, 1);
        return $results[0] ?? null;
    }

    /**
     * Find DTOs by some criteria, first by  looking DTOs in the cache,
     * otherwise use the IDs from each DTO to build an entry that can be cached
     *
     * Return the results sorted by the original criteria
     */
    public function findDTOsBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $ids = $this->findIdsBy($criteria, $orderBy, $limit, $offset);
        $cacheManager = $this->getCacheManager();
        $idField = $this->getIdField();
        if ($cacheManager->isEnabled()) {
            $cachedDtos = $cacheManager->getCachedDtos(self::class, $ids);
            $cachedIds = array_column($cachedDtos, $idField);
            $missedDtos = [];
            $missedIds = array_diff($ids, $cachedIds);
            if (count($missedIds)) {
                $missedDtos = $this->hydrateDTOsFromIds($missedIds);
                $cacheManager->cacheDtos(self::class, $missedDtos, $idField);
            }
            $dtos = array_values([...$cachedDtos, ...$missedDtos]);
        } else {
            $dtos = $this->hydrateDTOsFromIds($ids);
        }

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

    /**
     * Overridable find method which takes criteria and queries the DB for matching IDs
     * This is overridden in repositories when the criteria need to be changed
     * (for example into a DateTime)
     */
    protected function findIdsBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        return $this->doFindIdsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Look in the database for IDs matching this criteria
     */
    protected function doFindIdsBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $idField = $this->getIdField();
        $keys = array_keys($criteria);

        //if the only criteria is the IDs we don't need to look that up
        if ($keys === [$idField] && is_null($orderBy) && is_null($limit) && is_null($offset)) {
            return is_array($criteria[$idField]) ? $criteria[$idField] : [$criteria[$idField]];
        }
        $fields = [$idField];
        if (is_array($orderBy)) {
            $fields = array_unique([...$fields, ...array_keys($orderBy)]);
        }
        $dqlSelect = implode(', ', array_map(fn ($field) => "x.{$field}", $fields));
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($dqlSelect);
        $qb->distinct();
        $qb->from($this->getEntityName(), 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $results = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return array_column($results, $idField);
    }

    public function update(object $entity, bool $andFlush = true, bool $forceId = false): void
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

    public function delete(object $entity): void
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

    /**
     * @throws ConnectionException
     */
    //phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
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
        $mappings = $this->getClassMetadata()->getAssociationMappings();

        $manyToManyOwningSideMappings = array_filter(
            $mappings,
            fn(AssociationMapping $mapping)
                => in_array($mapping->fieldName, $related) && $mapping instanceof ManyToManyOwningSideMapping
        );

        $manyToManyInverseSideMapping = array_filter(
            $mappings,
            fn(AssociationMapping $mapping)
            => in_array($mapping->fieldName, $related) && $mapping instanceof ManyToManyInverseSideMapping
        );

        $inverseSideMetadata = $this->extractMetadataFromInverseSideMappings($manyToManyInverseSideMapping);

        $owningSideMetadata = $this->extractMetadataFromOwningSideMappings($manyToManyOwningSideMappings);

        $dtos = $this->attachDataFromManyToManyRelationshipsToDtos(
            $dtos,
            [...array_values($inverseSideMetadata), ...array_values($owningSideMetadata)],
        );

        $remainingRelated =  array_diff(
            $related,
            array_keys($manyToManyInverseSideMapping),
            array_keys($manyToManyOwningSideMappings),
        );

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

    protected function attachDataFromManyToManyRelationshipsToDtos(array $dtos, array $relationships): array
    {
        $ids = array_keys($dtos);
        $conn = $this->getEntityManager()->getConnection();
        foreach ($relationships as $rel) {
            $qb = $conn->createQueryBuilder();
            $qb->select($rel['relatedIdColumn'], $rel['dtoIdColumn'])
                ->from($rel['tableName'])
                ->where("{$rel['dtoIdColumn']} IN(:ids)")
                ->setParameter('ids', $ids, ArrayParameterType::STRING);
            $result = $qb->executeQuery()->fetchAllAssociative();
            foreach ($result as $row) {
                $dtos[$row[$rel['dtoIdColumn']]]->{$rel['fieldName']}[] = $row[$rel['relatedIdColumn']];
            }
        }

        return $dtos;
    }

    /**
     * @param ManyToManyOwningSideMapping[] $mappings
     */
    public function extractMetadataFromOwningSideMappings(array $mappings): array
    {
        return array_map(fn(ManyToManyOwningSideMapping $mapping) => [
            'fieldName' => $mapping->fieldName,
            'tableName'  => $mapping->joinTable->name,
            'dtoIdColumn'  => $mapping->joinTable->joinColumns[0]->name,
            'relatedIdColumn'  => $mapping->joinTable->inverseJoinColumns[0]->name,
        ], $mappings);
    }

    /**
     * @param ManyToManyInverseSideMapping[] $mappings
     */
    public function extractMetadataFromInverseSideMappings(array $mappings): array
    {
        $em = $this->getEntityManager();
        return array_map(function (ManyToManyInverseSideMapping $mapping) use ($em) {
            $metadata = $em->getClassMetadata($mapping->targetEntity);
            /** @var ManyToManyOwningSideMapping $owningSideMapping */
            $owningSideMapping = $metadata->getAssociationMapping($mapping->mappedBy);
            return [
                'fieldName' => $mapping->fieldName,
                'tableName'  => $owningSideMapping->joinTable->name,
                'dtoIdColumn'  => $owningSideMapping->joinTable->inverseJoinColumns[0]->name,
                'relatedIdColumn'  => $owningSideMapping->joinTable->joinColumns[0]->name,
            ];
        }, $mappings);
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
                if (is_array($value)) {
                    $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                } else {
                    $qb->andWhere($qb->expr()->eq("x.{$key}", ":{$key}"));
                }
                $qb->setParameter(":{$key}", $value);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy('x.' . $sort, $order);
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

    protected function getCacheManager(): DTOCacheManager
    {
        if (!isset($this->cacheManager)) {
            throw new Exception("The 'cacheManager' property is missing from " . self::class);
        }

        return $this->cacheManager;
    }
}
