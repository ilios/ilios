<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Term;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class LoadTermData
 */
class LoadTermData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('term');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadVocabularyData',
        ];
    }

    /**
     * @return TermInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Term();
    }

    /**
     * @param TermInterface $entity
     * @param array $data
     * @return TermInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `term_id`,`title`,`parent_term_id`, `description`, `vocabulary_id`, `active`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        if (! empty($data[2])) {
            $entity->setParent($this->getReference($this->getKey() . $data[2]));
        }
        $entity->setDescription($data[3]);
        $entity->setVocabulary($this->getReference('vocabulary' . $data[4]));
        $entity->setActive((boolean) $data[5]);
        return $entity;
    }
}
