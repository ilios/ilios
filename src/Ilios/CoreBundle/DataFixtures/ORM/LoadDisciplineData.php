<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Discipline;

/**
 * Class LoadDisciplineData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadDisciplineData extends AbstractFixture implements DependentFixtureInterface

{
    public function __construct()
    {
        parent::__construct('discipline');
    }
    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        //'`discipline_id`,`title`,`school_id`'
        $entity = new Discipline();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }
}
