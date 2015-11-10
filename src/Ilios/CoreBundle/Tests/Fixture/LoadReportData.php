<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Ilios\CoreBundle\Entity\Report;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadReportData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('ilioscore.dataloader.report')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Report();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setSubject($arr['subject']);
            if (array_key_exists('prepositionalObject', $arr)) {
                $entity->setPrepositionalObject($arr['prepositionalObject']);
            }
            if (array_key_exists('prepositionalObjectTableRowId', $arr)) {
                $entity->setPrepositionalObjectTableRowId($arr['prepositionalObjectTableRowId']);
            }
            $entity->setUser($this->getReference('users' . $arr['user']));
            $manager->persist($entity);
            $this->addReference('reports' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array('Ilios\CoreBundle\Tests\Fixture\LoadUserData');
    }
}
