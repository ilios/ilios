<?php

namespace Tests\App\Fixture;

use App\Entity\AssessmentOption;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAssessmentOptionData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('Tests\AppBundle\DataLoader\AssessmentOptionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AssessmentOption();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);

            $manager->persist($entity);
            $this->addReference('assessmentOptions' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
