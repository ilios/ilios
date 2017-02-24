<?php

namespace Ilios\ApiBundle\Service;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

class EndpointResponseNamer
{
    /**
     * @var string
     */
    protected $pathToEntities;

    public function __construct(KernelInterface $kernel)
    {
        $this->pathToEntities = $kernel->locateResource('@IliosCoreBundle/Entity');
    }

    public function getPluralName($object)
    {
        $list = $this->getEntityList();

        return $list[$object]['plural'];
    }

    public function getSingularName($object)
    {
        $list = $this->getEntityList();

        return $list[$object]['singular'];
    }

    protected function getEntityList()
    {
        $finder = new Finder();
        $files = $finder->in($this->pathToEntities)->files()->notName('*Interface.php')->sortByName();

        $list = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $name = $file->getBasename('.php');
            $plural = Inflector::pluralize($name);
            $key = strtolower($plural);
            $list[$key] = [
                'plural' => Inflector::camelize($plural),
                'singular' => Inflector::camelize($name),
            ];
        }

        return $list;
    }
}
