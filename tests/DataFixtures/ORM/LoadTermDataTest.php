<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\TermInterface;

/**
 * Class LoadTermDataTest
 */
class LoadTermDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\TermManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadTermData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadTermData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('term.csv');
    }

    /**
     * @param array $data
     * @param TermInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `term_id`,`title`,`term_parent_id`, `description`, `vocabulary_id`, `active`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        if (empty($data[2])) {
            $this->assertNull($entity->getParent());
        } else {
            $this->assertEquals($data[2], $entity->getParent()->getId());
        }
        $this->assertEquals($data[3], $entity->getDescription());
        $this->assertEquals($data[4], $entity->getVocabulary()->getId());
        $this->assertEquals((boolean) $data[4], $entity->isActive());
    }
}
