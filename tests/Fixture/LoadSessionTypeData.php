<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\SessionType;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionTypeData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\SessionTypeData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(SessionType::class);
        foreach ($data as $arr) {
            $entity = new SessionType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setCalendarColor($arr['calendarColor']);
            $entity->setAssessment($arr['assessment']);
            $entity->setActive($arr['active']);
            $entity->setAssessmentOption(
                $this->getReference('assessmentOptions' . $arr['assessmentOption'])
            );
            $entity->setSchool($this->getReference('schools' . $arr['school']));

            foreach ($arr['aamcMethods'] as $id) {
                $entity->addAamcMethod($this->getReference('aamcMethods' . $id));
            }
            $repository->update($entity, true, true);
            $this->addReference('sessionTypes' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadAamcMethodData',
            'App\Tests\Fixture\LoadAssessmentOptionData',
            'App\Tests\Fixture\LoadSchoolData',
        ];
    }
}
