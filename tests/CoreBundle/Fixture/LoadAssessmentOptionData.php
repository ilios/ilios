<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\AssessmentOption;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAssessmentOptionData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('Tests\CoreBundle\DataLoader\AssessmentOptionData')
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
