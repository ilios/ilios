<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearnerGroup;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLearnerGroupData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\LearnerGroupData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(LearnerGroup::class);
        foreach ($data as $arr) {
            $entity = new LearnerGroup();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            if (!empty($arr['location'])) {
                $entity->setUrl($arr['url']);
            }
            $entity->setNeedsAccommodation($arr['needsAccommodation']);

            if (!empty($arr['location'])) {
                $entity->setLocation($arr['location']);
            }
            if (!empty($arr['parent'])) {
                $entity->setParent($this->getReference('learnerGroups' . $arr['parent']));
            }
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor($this->getReference('learnerGroups' . $arr['ancestor']));
            }
            $entity->setCohort($this->getReference('cohorts' . $arr['cohort']));
            foreach ($arr['users'] as $id) {
                $entity->addUser($this->getReference('users' . $id));
            }
            foreach ($arr['instructors'] as $id) {
                $entity->addInstructor($this->getReference('users' . $id));
            }
            foreach ($arr['instructorGroups'] as $id) {
                $entity->addInstructorGroup($this->getReference('instructorGroups' . $id));
            }
            $repository->update($entity, false, true);
            $this->addReference('learnerGroups' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadInstructorGroupData',
        ];
    }
}
