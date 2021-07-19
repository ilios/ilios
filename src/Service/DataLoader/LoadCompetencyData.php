<?php

declare(strict_types=1);

namespace App\Service\DataLoader;

use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Competency;
use App\Entity\CompetencyInterface;

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
            'App\DataFixtures\ORM\LoadSchoolData',
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
     * @return CompetencyInterface
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
        $entity->setActive((bool) $data[4]);
        return $entity;
    }
}
