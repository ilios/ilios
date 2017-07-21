<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\Alert;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAlertData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('Tests\CoreBundle\DataLoader\AlertData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Alert();
            $entity->setId($arr['id']);
            $entity->setTableName($arr['tableName']);
            $entity->setTableRowId($arr['tableRowId']);
            $entity->setDispatched($arr['dispatched']);
            if (array_key_exists('additionalText', $arr)) {
                $entity->setAdditionalText($arr['additionalText']);
            }
            foreach ($arr['changeTypes'] as $id) {
                $entity->addChangeType($this->getReference('alertChangeTypes' . $id));
            }
            foreach ($arr['instigators'] as $id) {
                $entity->addInstigator($this->getReference('users' . $id));
            }
            foreach ($arr['recipients'] as $id) {
                $entity->addRecipient($this->getReference('schools' . $id));
            }
            $manager->persist($entity);
            $this->addReference('alerts' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadAlertChangeTypeData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadUserData'
        );
    }
}
