<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Alert;
use App\Entity\AlertChangeType;
use App\Entity\School;
use App\Entity\User;
use App\Tests\DataLoader\AlertData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadAlertData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected AlertData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
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
                $entity->addChangeType($this->getReference('alertChangeTypes' . $id, AlertChangeType::class));
            }
            foreach ($arr['instigators'] as $id) {
                $entity->addInstigator($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['recipients'] as $id) {
                $entity->addRecipient($this->getReference('schools' . $id, School::class));
            }
            $manager->persist($entity);
            $this->addReference('alerts' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadAlertChangeTypeData::class,
            LoadSchoolData::class,
            LoadUserData::class,
        ];
    }
}
