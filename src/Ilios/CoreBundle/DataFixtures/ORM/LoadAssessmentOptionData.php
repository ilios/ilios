<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AssessmentOption;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class LoadAssessmentOptionData
 */
class LoadAssessmentOptionData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('assessment_option');
    }

    /**
     * @return AssessmentOptionInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new AssessmentOption();
    }

    /**
     * @param AssessmentOptionInterface $entity
     * @param array $data
     * @return AssessmentOptionInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `assessment_option_id`,`name`
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        return $entity;
    }
}
