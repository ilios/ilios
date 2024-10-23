<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Inflector\Inflector;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class EndpointResponseNamer
 * Handles the pluralization and string manipulation to go from
 * the value our api expects like 'userrole' to what we may need
 * to return like 'userRoles'
 *
 */
class EndpointResponseNamer
{
    /**
     * @var string pointing to all of our entity classes
     */
    protected string $pathToEntities;

    private const LIST_CACHE_KEY = 'endpoint-response-namer-entity-list';

    /**
     * EndpointResponseNamer constructor.
     * Extracts the entity path from the Kernel
     */
    public function __construct(
        KernelInterface $kernel,
        protected Inflector $inflector,
        protected CacheInterface $appCache
    ) {
        $this->pathToEntities = $kernel->getProjectDir() . '/src/Entity';
    }

    /**
     * Get the pluralized name of an endpoint
     *
     */
    public function getPluralName(string $endpointKey): string
    {
        $list = $this->getEntityList();

        return $list[$endpointKey]['plural'];
    }

    /**
     * Get the singular name for an endpoint
     *
     */
    public function getSingularName(string $endpointKey): string
    {
        $list = $this->getEntityList();

        return $list[strtolower($endpointKey)]['singular'];
    }

    /**
     * Create a list of all the plural and singular names for entities
     */
    protected function getEntityList(): array
    {
        $pathToEntities = $this->pathToEntities;
        $inflector = $this->inflector;
        return $this->appCache->get(self::LIST_CACHE_KEY, function () use ($pathToEntities, $inflector) {
            $finder = new Finder();
            $files = $finder->in($pathToEntities)->files()->notName('*Interface.php')->sortByName();

            $list = [];
            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $name = $file->getBasename('.php');
                $plural = $inflector->pluralize($name);
                $key = strtolower($plural);
                $list[$key] = [
                    'plural' => $inflector->camelize($plural),
                    'singular' => $inflector->camelize($name),
                ];
            }

            return $list;
        });
    }
}
