<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\AamcPcrsManagerInterface;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Class LoadAamcPcrsDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadAamcPcrsDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'aamc_pcrs.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.aamcpcrs.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcPcrsData',
        ];
    }

    /**
     * @param array $data
     * @param AamcPcrsInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `method_id`,`description`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getDescription());
    }

    /**
     * @param array $data
     * @return AamcPcrsInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var AamcPcrsManagerInterface $em
         */
        $em = $this->em;
        return $em->findAamcPcrsBy(['id' => $data[0]]);
    }
}
