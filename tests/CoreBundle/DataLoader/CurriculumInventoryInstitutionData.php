<?php

namespace Tests\CoreBundle\DataLoader;

class CurriculumInventoryInstitutionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'name' => $this->faker->text(25),
            'aamcCode' => "{$this->faker->randomDigit}",
            'addressStreet' => '221 West',
            'addressCity' => $this->faker->city,
            'addressStateOrProvince' => $this->faker->stateAbbr,
            'addressZipCode' => $this->faker->postcode,
            'addressCountryCode' => 'UK',
            'school' => '1'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 2,
            'name' => $this->faker->text(25),
            'aamcCode' => "{$this->faker->randomDigit}",
            'addressStreet' => '12 Main',
            'addressCity' => $this->faker->city,
            'addressStateOrProvince' => $this->faker->stateAbbr,
            'addressZipCode' => $this->faker->postcode,
            'addressCountryCode' => 'US',
            'school' => '2'
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
