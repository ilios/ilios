<?php

namespace Tests\AppBundle\Fixture;

use AppBundle\Entity\Vocabulary;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadVocabularyData extends AbstractFixture implements
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
            ->get('Tests\AppBundle\DataLoader\VocabularyData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Vocabulary();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setActive($arr['active']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            $manager->persist($entity);
            $this->addReference('vocabularies' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadSchoolData',
        );
    }
}
