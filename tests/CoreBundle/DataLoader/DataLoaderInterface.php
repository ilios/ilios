<?php

namespace Tests\CoreBundle\DataLoader;

interface DataLoaderInterface
{
    /**
     * Get a single item from this loader
     * @return array
     */
    public function getOne();

    /**
     * Get all items from this loader
     * @return array
     */
    public function getAll();

     /**
      * Create a sample of this item
      * @return array
      */
    public function create();

      /**
       * Create an invalid sample of this item
       * @return array
       */
    public function createInvalid();

      /**
       * Create multiple samples of this item
       * @param integer $count
       * @return array
       */
    public function createMany($count);
}
