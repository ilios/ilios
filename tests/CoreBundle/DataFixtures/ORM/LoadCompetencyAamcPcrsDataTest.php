<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcPcrsInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class LoadCompetencyAmcPcrsDataTest
 */
class LoadCompetencyAmcPcrsDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\CompetencyManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadCompetencyAamcPcrsData',
        ];
    }
    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadCompetencyAamcPcrsData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('competency_x_aamc_pcrs.csv');
    }

    /**
     * @param array $data
     * @param CompetencyInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `competency_id`,`pcrs_id`
        $this->assertEquals($data[0], $entity->getId());
        // find the PCRS
        $pcrsId = $data[1];
        $pcrs = $entity->getAamcPcrses()->filter(function (AamcPcrsInterface $pcrs) use ($pcrsId) {
            return $pcrs->getId() === $pcrsId;
        })->first();
        $this->assertNotEmpty($pcrs);
    }
}
