<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class LoadSchoolDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadSchoolDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.school.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('school.csv');
    }

    /**
     * @param array $data
     * @param SchoolInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `school_id`,`template_prefix`,`title`,`ilios_administrator_email`,`change_alert_recipients`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTemplatePrefix());
        $this->assertEquals($data[2], $entity->getTitle());
        $this->assertEquals($data[3], $entity->getIliosAdministratorEmail());
        $this->assertEquals($data[4], $entity->getChangeAlertRecipients());
    }

    /**
     * @param array $data
     * @return SchoolInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var SchoolManagerInterface $em
         */
        $em = $this->em;
        return $em->findSchoolBy(['id' => $data[0]]);
    }
}
