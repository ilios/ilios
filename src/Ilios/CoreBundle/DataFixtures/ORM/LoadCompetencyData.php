<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Competency;

/**
 * Class LoadCompetencyData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadCompetencyData extends AbstractFixture implements DependentFixtureInterface

{
    public function __construct()
    {
        parent::__construct('competency');
    }
    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `competency_id`,`title`,`parent_competency_id`,`school_id`
        $entity = new Competency();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        if (! empty($data[2])) {
            $entity->setParent($this->getReference($this->getKey() . $data[2]));
        }
        $entity->setSchool($this->getReference('school' . $data[3]));
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
