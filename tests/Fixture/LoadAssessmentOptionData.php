<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AssessmentOption;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAssessmentOptionData extends AbstractFixture implements
    ORMFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\AssessmentOptionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AssessmentOption();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);

            $manager->persist($entity);
            $this->addReference('assessmentOptions' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}
