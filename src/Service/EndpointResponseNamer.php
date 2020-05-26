<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Inflector\Inflector;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

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
    protected $pathToEntities;

    /**
     * @var Inflector
     */
    protected $inflector;

    /**
     * EndpointResponseNamer constructor.
     * Extracts the entity path from the Kernel
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel, Inflector $inflector)
    {
        $this->pathToEntities = $kernel->getProjectDir() . '/src/Entity';
        $this->inflector = $inflector;
    }

    /**
     * Get the pluralized name of an endpoint
     *
     * @param $object
     * @return mixed
     */
    public function getPluralName($object)
    {
        $list = $this->getEntityList();

        return $list[$object]['plural'];
    }

    /**
     * Get the singular name for an endpoint
     *
     * @param $object
     * @return mixed
     */
    public function getSingularName($object)
    {
        $list = $this->getEntityList();

        return $list[$object]['singular'];
    }

    /**
     * Create a list of all the plural and singular names for entities
     * @return array
     */
    protected function getEntityList()
    {
        $finder = new Finder();
        $files = $finder->in($this->pathToEntities)->files()->notName('*Interface.php')->sortByName();

        $list = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $name = $file->getBasename('.php');
            $plural = $this->inflector->pluralize($name);
            $key = strtolower($plural);
            $list[$key] = [
                'plural' => $this->inflector->camelize($plural),
                'singular' => $this->inflector->camelize($name),
            ];
        }

        return $list;
    }
}
