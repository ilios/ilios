<?php

declare(strict_types=1);

namespace App\Service;

use DateInterval;
use Flagception\Manager\FeatureManagerInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DTOCacheManager
{
    private const string CACHE_KEY_SEPARATOR = 'xxDTOxx';
    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected CacheItemPoolInterface $psr6Cache,
        protected CacheInterface $cacheContract,
        protected FeatureManagerInterface $featureManager,
    ) {
    }

    /**
     * Check if caching of DTOs is enabled
     */
    public function isEnabled(): bool
    {
        return $this->featureManager->isActive('dto_caching');
    }

    /**
     * Cache and tag a set of DTOs
     */
    public function cacheDtos(string $name, array $dtos, string $idField): void
    {
        $tagger = $this;
        foreach ($dtos as $dto) {
            $this->cacheContract->get(
                $this->getDtoCacheKey($name, $dto->$idField),
                function (ItemInterface $item) use ($dto, $tagger) {
                    $tagger->tag($item, $dto);
                    return $dto;
                }
            );
        }
    }

    /**
     * Get DTOs from the cache
     */
    public function getCachedDtos(string $name, array $ids): array
    {
        $cacheIds = array_map(fn(mixed $id) => $this->getDtoCacheKey($name, $id), $ids);
        $items = $this->psr6Cache->getItems($cacheIds);
        $rhett = [];
        /** @var CacheItemInterface $item */
        foreach ($items as $item) {
            if ($item->isHit()) {
                $rhett[] = $item->get();
            }
        }
        return $rhett;
    }


    /**
     * Tag a cache item with all the relationships and data for a DTO
     */
    protected function tag(ItemInterface $item, object $dto): void
    {
        $tags = $this->getTagsForDto($dto);
        $item->tag($tags);
        $item->expiresAfter(DateInterval::createFromDateString('1 day'));
    }

    /**
     * Parse out the parts of a DTO
     * Then, for each relationship, add a tag to the cache
     */
    protected function getTagsForDto(object $dto): array
    {
        $tags = [];
        $reflection = new ReflectionClass($dto);
        $type = $this->entityMetadata->extractType($reflection);
        $entity = $this->entityMetadata->getEntityForType($type);
        $idProperty = $this->entityMetadata->extractId($reflection);
        $tags[] = self::getTag($entity, $dto->$idProperty);
        $tags[] = self::getTag($entity, false);

        $related = $this->entityMetadata->extractRelated($reflection);
        foreach ($related as $name => $type) {
            $entity = $this->entityMetadata->getEntityForType($type);
            if (is_array($dto->$name)) {
                foreach ($dto->$name as $id) {
                    $tags[] = self::getTag($entity, $id);
                }
            } elseif ($dto->$name) {
                $tags[] = self::getTag($entity, $dto->$name);
            }
        }
        return $tags;
    }

    /**
     * Get the tag for an entity/id combo.
     * We do this here because we can't actually store the FQN for the entity
     * because slash isn't an allowed character.
     * When no id is sent we create a tag with just the entity name which is used
     * when a new value is created to invalidate anything that touched the original.
     */
    public static function getTag(string $entity, int|string|false $id): string
    {
        // backslashes aren't allowed in tags, remove them from our name
        $name = str_replace('\\', '', $entity);
        return $name . ($id ? "-{$id}" : '');
    }

    /**
     * Construct a consistent cache key for a DTO which doesn't contain reserved characters.
     */
    protected function getDtoCacheKey(string $name, mixed $id): string
    {
        // backslashes aren't allowed in keys, remove them from our name
        $name = str_replace('\\', '', $name);
        return $name . self::CACHE_KEY_SEPARATOR . $id;
    }

    /**
     * Extract the ID from a cache key for a DTO
     */
    protected function getDtoIdFromCacheKey(string $key): string
    {
        $arr = explode(self::CACHE_KEY_SEPARATOR, $key);
        return $arr[1];
    }
}
