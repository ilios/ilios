<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshTerm;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshTermData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\MeshTermData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(MeshTerm::class);
        foreach ($data as $arr) {
            $entity = new MeshTerm();
            $entity->setId($arr['id']);
            $entity->setMeshTermUid($arr['meshTermUid']);
            $entity->setName($arr['name']);
            $entity->setLexicalTag($arr['lexicalTag']);
            $entity->setConceptPreferred($arr['conceptPreferred']);
            $entity->setRecordPreferred($arr['recordPreferred']);
            $entity->setPermuted($arr['permuted']);
            foreach ($arr['concepts'] as $id) {
                $entity->addConcept($this->getReference('meshConcepts' . $id));
            }
            $this->addReference('meshTerms' . $arr['id'], $entity);
            $repository->update($entity, false, true);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadMeshConceptData',
        ];
    }
}
