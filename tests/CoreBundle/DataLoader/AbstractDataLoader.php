<?php

namespace Tests\CoreBundle\DataLoader;

use Faker\Factory as FakerFactory;

/**
 * Abstract utilities for loading data
 *
 *
 */
abstract class AbstractDataLoader implements DataLoaderInterface
{
    protected $data;

    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
        $this->faker->seed(1234);
    }


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
        if (!empty($this->data)) {
            return;
        }

        $this->data = $this->getData();
    }


    public function getOne()
    {
        $this->setUp();
        return array_values($this->data)[0];
    }

    public function getAll()
    {
        $this->setUp();
        return $this->data;
    }

    /**
     * Get a formatted data from a string
     * @param string $when
     * @return string
     */
    public function getFormattedDate($when)
    {
        $dt = new \DateTime($when);
        return $dt->format('c');
    }

    abstract public function create();

    abstract public function createInvalid();

    /**
     * @inheritdoc
     */
    public function createMany($count)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $data[] = $arr;
        }

        return $data;
    }
}
