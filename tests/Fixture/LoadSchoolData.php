<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\School;
use App\Tests\DataLoader\SchoolData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadSchoolData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected SchoolData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new School();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            if (array_key_exists('templatePrefix', $arr)) {
                $entity->setTemplatePrefix($arr['templatePrefix']);
            }
            $entity->setIliosAdministratorEmail($arr['iliosAdministratorEmail']);
            $entity->setChangeAlertRecipients($arr['changeAlertRecipients']);
            $manager->persist($entity);
            $this->addReference('schools' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}
