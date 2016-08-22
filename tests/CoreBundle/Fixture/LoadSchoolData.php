<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\School;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSchoolData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.school')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new School();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setIliosAdministratorEmail($arr['iliosAdministratorEmail']);
            $entity->setChangeAlertRecipients($arr['changeAlertRecipients']);
            $manager->persist($entity);
            $this->addReference('schools' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}
