<?php

declare(strict_types=1);

namespace App\Service\DataLoader;

use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Term;
use App\Entity\TermInterface;

/**
 * Class LoadTermData
 */
class LoadTermData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'term');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'App\DataFixtures\ORM\LoadVocabularyData',
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
     * @return TermInterface
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
        $entity->setActive((bool) $data[5]);
        return $entity;
    }
}
