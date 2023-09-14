<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\IlmSession;
use App\Repository\RepositoryInterface;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Tests\DataLoader\IlmSessionData;

class LoadIlmSessionData extends AbstractFixture implements
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
            ->get(IlmSessionData::class)
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(IlmSession::class);
        foreach ($data as $arr) {
            $entity = new IlmSession();
            $entity->setId($arr['id']);
            $entity->setHours($arr['hours']);
            $entity->setDueDate(new DateTime($arr['dueDate']));
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id));
            }
            foreach ($arr['learnerGroups'] as $id) {
                $entity->addLearnerGroup($this->getReference('learnerGroups' . $id));
            }
            foreach ($arr['learners'] as $id) {
                $entity->addLearner($this->getReference('users' . $id));
            }
            $repository->update($entity, true, true);
            $this->addReference('ilmSessions' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadInstructorGroupData',
            'App\Tests\Fixture\LoadLearnerGroupData',
            'App\Tests\Fixture\LoadSessionData',
        ];
    }
}
