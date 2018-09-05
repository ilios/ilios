<?php

namespace Tests\AppBundle\Fixture;

use AppBundle\Entity\Term;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTermData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\AppBundle\DataLoader\TermData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Term();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $entity->setActive($arr['active']);
            $entity->setVocabulary($this->getReference('vocabularies' . $arr['vocabulary']));
            if (isset($arr['parent'])) {
                $entity->setParent($this->getReference('terms' . $arr['parent']));
            }
            foreach ($arr['aamcResourceTypes'] as $id) {
                $entity->addAamcResourceType($this->getReference('aamcResourceTypes' . $id));
            }
            $manager->persist($entity);
            $this->addReference('terms' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadVocabularyData',
            'Tests\AppBundle\Fixture\LoadAamcResourceTypeData',
        );
    }
}
