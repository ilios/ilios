<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionTypes extends AbstractDataLoader
{
    protected function getData()
    {
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
