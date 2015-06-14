<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

/**
 * Abstract utilities for loading data
 *
 * @package Ilios\CoreBundle\Tests\DataLoader
 *
 */
abstract class AbstractDataLoader implements DataLoaderInterface
{
    private static $data;

    /**
     * Create test data
     * @return array
     */
    abstract protected function getData();

    /**
     * [setup description]
     * @return [type] [description]
     */
    protected function setup()
    {
        if (!empty(self::$data)) {
            return;
        }

        self::$data = $this->getData();
    }


    public function getOne()
    {
        $this->setUp();
        return array_values(self::$data)[0];
    }

    public function getAll()
    {
        $this->setUp();
        return self::$data;
    }

    abstract public function create();

    abstract public function createInvalid();
}
