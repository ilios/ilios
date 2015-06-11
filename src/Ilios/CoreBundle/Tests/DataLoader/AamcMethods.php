<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AamcMethods implements DataLoaderInterface
{
    private static $data;

    protected function setup()
    {
        if (!empty(self::$data)) {
            return;
        }
        $arr = array();

        $arr[] = array(
          'id' => "AM001",
          'description'=> "Clinical Documentation Review",
          'sessionTypes' => ['134','152']
        );

        self::$data = $arr;
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

    public function create()
    {
        return [
            //aamc methods requires an id it is not auto generated
            'id'  => 'aamcTestId',
            'description' => 'test description',
            'sessionTypes' => ['152']
        ];
    }

    public function createInvalid()
    {
        return [
            'description' => null,
            'sessionTypes' => 'a string?'
        ];
    }
}
