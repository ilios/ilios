<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Vocabulary;
use Ilios\CoreBundle\Entity\VocabularyInterface;

/**
 * Class LoadVocabularyData
 */
class LoadVocabularyData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('vocabulary');
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
     * @return VocabularyInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Vocabulary();
    }

    /**
     * @param VocabularyInterface $entity
     * @param array $data
     * @return VocabularyInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `vocabulary_id`,`title`,`school_id`, `active`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($this->getReference('school' . $data[2]));
        $entity->setActive((boolean) $data[3]);
        return $entity;
    }
}
