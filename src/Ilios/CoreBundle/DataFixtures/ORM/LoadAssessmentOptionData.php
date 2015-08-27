<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AssessmentOption;

/**
 * Class LoadAssessmentOptionData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadAssessmentOptionData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('assessment_option');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `assessment_option_id`,`name`
        $entity = new AssessmentOption();
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        return $entity;
    }
}
