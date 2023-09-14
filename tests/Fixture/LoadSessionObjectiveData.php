<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\SessionObjective;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionObjectiveData extends AbstractFixture implements
    ORMFixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\SessionObjectiveData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(SessionObjective::class);
        foreach ($data as $arr) {
            $entity = new SessionObjective();
            $entity->setId($arr['id']);
            $entity->setPosition($arr['position']);
            $entity->setActive($arr['active']);
            $entity->setTitle($arr['title']);
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor($this->getReference('sessionObjectives' . $arr['ancestor']));
            }
            $repository->update($entity, true, true);

            $this->addReference('sessionObjectives' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSessionData',
        ];
    }
}
