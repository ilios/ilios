<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\AamcPcrsInterface;
use App\Entity\CompetencyInterface;
use App\Repository\CompetencyRepository;

class LoadCompetencyAamcPcrsDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return CompetencyRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadCompetencyAamcPcrsData',
        ];
    }
    /**
     * @covers \App\DataFixtures\ORM\LoadCompetencyAamcPcrsData::load
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
