<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\School;
use App\Entity\User;
use App\Tests\DataLoader\ReportData;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Report;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadReportData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected ReportData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
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
            $entity->setUser($this->getReference('users' . $arr['user'], User::class));

            if (!empty($arr['school'])) {
                $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            }
            $manager->persist($entity);
            $this->addReference('reports' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadUserData::class,
            LoadSchoolData::class,
        ];
    }
}
