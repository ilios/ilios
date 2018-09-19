<?php

namespace App\DataFixtures\ORM;

use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\Department;
use App\Entity\DepartmentInterface;
use App\Traits\IdentifiableEntityInterface;

/**
 * Class LoadDepartmentData
 */
class LoadDepartmentData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'department');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @return DepartmentInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Department();
    }

    /**
     * @param DepartmentInterface $entity
     * @param array $data
     * @return IdentifiableEntityInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `department_id`,`title`,`school_id`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        return $entity;
    }
}
