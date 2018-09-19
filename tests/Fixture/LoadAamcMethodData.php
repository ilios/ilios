<?php

namespace Tests\App\Fixture;

use App\Entity\AamcMethod;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAamcMethodData extends AbstractFixture implements
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
            ->get('Tests\App\DataLoader\AamcMethodData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AamcMethod();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            
            $manager->persist($entity);
            $this->addReference('aamcMethods' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
