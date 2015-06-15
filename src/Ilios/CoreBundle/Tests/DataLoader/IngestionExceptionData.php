<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class IngestionExceptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
