<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use AppBundle\Entity\Competency;
use AppBundle\Entity\CompetencyInterface;

/**
 * Class LoadCompetencyData
 */
class LoadCompetencyData extends AbstractFixture implements DependentFixtureInterface
{

    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'competency');
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
     * @return CompetencyInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Competency();
    }

    /**
     * @param CompetencyInterface $entity
     * @param array $data
     * @return CompetencyInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `competency_id`,`title`,`parent_competency_id`,`school_id`, `active`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        if (! empty($data[2])) {
            $entity->setParent($this->getReference($this->getKey() . $data[2]));
        }
        $entity->setSchool($this->getReference('school' . $data[3]));
        $entity->setActive((boolean) $data[4]);
        return $entity;
    }
}
