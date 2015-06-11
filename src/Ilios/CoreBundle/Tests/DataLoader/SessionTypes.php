<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionTypes implements DataLoaderInterface
{
    private static $data;

    protected function setup()
    {
        if (!empty(self::$data)) {
            return;
        }
        $arr = array();

        $arr[134] = array(
          'id' => "134",
          'title'=> "Clinical Documentation Review",
          'assessmentOption' => "1",
          'owningSchool' => "1",
          'aamcMethods' => [
            "AM001"
          ],
          'sessions' => []
        );

        $arr[152] = array(
          'id' => "152",
          'title'=> "Clinical Documentation Review [formative]",
          'assessmentOption' => "2",
          'owningSchool' => "1",
          'aamcMethods' => [
            "AM001"
          ],
          'sessions' => array()
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
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
