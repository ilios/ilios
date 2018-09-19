<?php

namespace Tests\App\Fixture;

use App\Entity\Objective;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadObjectiveData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\AppBundle\DataLoader\ObjectiveData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Objective();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setPosition($arr['position']);
            if (array_key_exists('competency', $arr)) {
                $entity->setCompetency($this->getReference('competencies' . $arr['competency']));
            }
            foreach ($arr['parents'] as $id) {
                $entity->addParent($this->getReference('objectives' . $id));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            if (array_key_exists('ancestor', $arr)) {
                $entity->setAncestor($this->getReference('objectives' . $arr['ancestor']));
            }
            $manager->persist($entity);
            $this->addReference('objectives' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadCompetencyData',
            'Tests\AppBundle\Fixture\LoadMeshDescriptorData',
        );
    }
}
