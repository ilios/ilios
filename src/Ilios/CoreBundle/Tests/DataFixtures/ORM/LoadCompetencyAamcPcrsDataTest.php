<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcPcrsInterface;
use Ilios\CoreBundle\Entity\Manager\CompetencyManagerInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class LoadCompetencyAmcPcrsDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadCompetencyAmcPcrsDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'competency_x_aamc_pcrs.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.competency.manager';
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

    /**
     * @param array $data
     * @return CompetencyInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var CompetencyManagerInterface $em
         */
        $em = $this->em;
        return $em->findCompetencyBy(['id' => $data[0]]);
    }
}
