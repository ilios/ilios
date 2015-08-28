<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\AamcMethodManagerInterface;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class LoadAamcMethodDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadAamcMethodDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'aamc_method.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.aamcmethod.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcMethodData',
        ];
    }

    /**
     * @param array $data
     * @param AamcMethodInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `method_id`,`description`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getDescription());
    }

    /**
     * @param array $data
     * @return AamcMethodInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var AamcMethodManagerInterface $em
         */
        $em = $this->em;
        return $em->findAamcMethodBy(['id' => $data[0]]);
    }
}
