<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\MeshTerm;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMeshTermData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\CoreBundle\DataLoader\MeshTermData')
            ->getAll();
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
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
        );
    }
}
