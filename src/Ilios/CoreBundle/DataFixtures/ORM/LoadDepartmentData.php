<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Department;

/**
 * Class LoadDepartmentData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadDepartmentData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('department');
    }
    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `department_id`,`title`,`school_id`,`deleted`
        $entity = new Department();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        $entity->setDeleted((boolean) $data[3]);
        return $entity;
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
}
