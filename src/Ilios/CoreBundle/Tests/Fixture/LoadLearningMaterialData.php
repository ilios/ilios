<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\LearningMaterials\File as FileLearningMaterial;
use Ilios\CoreBundle\Entity\LearningMaterials\Citation as CitationLearningMaterial;
use Ilios\CoreBundle\Entity\LearningMaterials\Link as LinkLearningMaterial;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLearningMaterialData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('ilioscore.dataloader.learningMaterial')
            ->getAll();
        foreach ($data as $arr) {
            switch($arr['type']){
                case 'citation':
                    $entity = new CitationLearningMaterial();
                    break;
                case 'link':
                    $entity = new LinkLearningMaterial();
                    break;
                case 'file':
                    $entity = new FileLearningMaterial();
                    $entity->setCopyrightPermission($arr['copyrightPermission']);
                    $entity->setCopyrightRationale($arr['copyrightRationale']);
                    break;
            }

            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $entity->setOriginalAuthor($arr['originalAuthor']);
            $entity->setToken($arr['token']);
            $entity->setUserRole($this->getReference('learningMaterialUserRoles' . $arr['userRole']));
            $entity->setStatus($this->getReference('learningMaterialStatus' . $arr['status']));
            $entity->setOwningUser($this->getReference('users' . $arr['owningUser']));

            $manager->persist($entity);
            $this->addReference('learningMaterials' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialStatusData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
        );
    }
}
