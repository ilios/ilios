<?php

declare(strict_types=1);

namespace App\Service;

use DateInterval;
use ReflectionClass as ReflectionClassAlias;
use Symfony\Contracts\Cache\ItemInterface;

class DTOCacheTagger
{
    public function __construct(protected EntityMetadata $entityMetadata)
    {
    }

    /**
     * Tag a cache item with all the relationships and data for a DTO
     */
    public function tag(ItemInterface $item, object $dto): void
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
        $reflection = new ReflectionClassAlias($dto);
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
        return $name . ($id ? "-${id}" : '');
    }
}
