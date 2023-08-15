<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Alert;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAlertData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
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
            ->get('App\Tests\DataLoader\AlertData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(Alert::class);
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
            $repository->update($entity, true, true);
            $this->addReference('alerts' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadAlertChangeTypeData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadUserData'
        ];
    }
}
