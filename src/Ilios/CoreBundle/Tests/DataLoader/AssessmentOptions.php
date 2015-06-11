<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AssessmentOptions implements DataLoaderInterface
{
    private static $data;

    protected function setup()
    {
        if (!empty(self::$data)) {
            return;
        }
        $arr = array();

        $arr[1] = array(
          'id' => "1",
          'name' => 'summative',
          'sessionTypes' => [
              "27",
              "134",
              "135",
              "137",
              "138",
              "139",
              "140",
              "141",
              "142",
              "143",
              "145",
              "148",
              "149",
              "151"
          ]
        );

        $arr[2] = array(
          'id' => "2",
          'name' => 'formative',
          'sessionTypes' => [
              "144",
              "146",
              "147",
              "150",
              "152",
              "153",
              "154",
              "155",
              "156",
              "157",
              "158"
          ]
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
