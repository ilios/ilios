<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Discipline;
use Ilios\CoreBundle\Entity\DisciplineInterface;

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
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @return DisciplineInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Discipline();
    }

    /**
     * @param DisciplineInterface $entity
     * @param array $data
     * @return DisciplineInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `discipline_id`,`title`,`school_id`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        return $entity;
    }
}
