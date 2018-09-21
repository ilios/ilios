<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\VocabularyInterface;

/**
 * Class LoadVocabularyDataTest
 */
class LoadVocabularyDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\VocabularyManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadVocabularyData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadVocabularyData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('vocabulary.csv');
    }

    /**
     * @param array $data
     * @param VocabularyInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `vocabulary_id`,`title`,`school_id`,`active`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
        $this->assertEquals((boolean) $data[3], $entity->isActive());
    }
}
