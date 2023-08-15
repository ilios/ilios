<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Vocabulary;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadVocabularyData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\VocabularyData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(Vocabulary::class);
        foreach ($data as $arr) {
            $entity = new Vocabulary();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setActive($arr['active']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            $repository->update($entity, false, true);
            $this->addReference('vocabularies' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadSchoolData',
        ];
    }
}
