<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AamcMethods extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
          'id' => "AM001",
          'description'=> "Clinical Documentation Review",
          'sessionTypes' => ['134','152']
        );

        return $arr;
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
