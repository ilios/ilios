<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\DisciplineManagerInterface;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Class LoadDisciplineDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadDisciplineDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'discipline.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.discipline.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadDisciplineData',
        ];
    }

    /**
     * @param array $data
     * @param DisciplineInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `discipline_id`,`title`,`school_id`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
    }

    /**
     * @param array $data
     * @return DisciplineInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var DisciplineManagerInterface $em
         */
        $em = $this->em;
        return $em->findDisciplineBy(['id' => $data[0]]);
    }
}
