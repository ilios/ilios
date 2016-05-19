<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\SessionTypeManagerInterface;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class LoadSessionTypeDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadSessionTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.sessiontype.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSessionTypeData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadSessionTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('session_type.csv');
    }

    /**
     * @param array $data
     * @param SessionTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `session_type_id`,`title`,`school_id`,`session_type_css_class`,`assessment`,`assessment_option_id`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
        $this->assertEquals($data[3], $entity->getSessionTypeCssClass());
        $this->assertEquals((boolean) $data[4], $entity->isAssessment());
        if (empty($data[5])) {
            $this->assertNull($entity->getAssessmentOption());
        } else {
            $this->assertEquals($data[5], $entity->getAssessmentOption());
        }
    }
}
