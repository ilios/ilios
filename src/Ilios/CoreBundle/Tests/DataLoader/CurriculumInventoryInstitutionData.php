<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryInstitutionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'name' => $this->faker->text(25),
            'aamcCode' => $this->faker->randomDigit,
            'addressStreet' => $this->faker->streetAddress,
            'addressCity' => $this->faker->city,
            'addressStateOrProvince' => $this->faker->stateAbbr,
            'addressZipCode' => $this->faker->postcode,
            'addressCountryCode' => $this->faker->country,
            'school' => '1'
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
