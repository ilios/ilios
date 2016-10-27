<?php

namespace Tests\CoreBundle\DataLoader;

use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

class LearningMaterialStatusData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => LearningMaterialStatusInterface::IN_DRAFT,
            'title' => 'Draft'
        );
        $arr[] = array(
            'id' => LearningMaterialStatusInterface::FINALIZED,
            'title' => 'Final'
        );
        $arr[] = array(
            'id' => LearningMaterialStatusInterface::REVISED,
            'title' => 'Revised'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => $this->faker->text(10)
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
