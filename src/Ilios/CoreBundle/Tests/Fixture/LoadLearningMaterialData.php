<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\LearningMaterial;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

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
        $fs = new Filesystem();
        $storePath = $this->container->getParameter('ilios_core.file_store_path');
        foreach ($data as $arr) {
            $entity = new LearningMaterial();
            if (array_key_exists('id', $arr)) {
                $entity->setId($arr['id']);
            }
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $entity->setOriginalAuthor($arr['originalAuthor']);
            $entity->setCopyrightRationale($arr['copyrightRationale']);
            $entity->setCopyrightPermission($arr['copyrightPermission']);
            $entity->setUserRole($this->getReference('learningMaterialUserRoles' . $arr['userRole']));
            $entity->setStatus($this->getReference('learningMaterialStatus' . $arr['status']));
            $entity->setOwningUser($this->getReference('users' . $arr['owningUser']));
            $optional = [
                'link',
                'citation',
                'filename',
                'mimetype',
                'filesize',
                'token',
            ];
            foreach ($optional as $key) {
                if (array_key_exists($key, $arr)) {
                    $method = 'set' . ucfirst($key);
                    $entity->$method($arr[$key]);
                }
            }
            if (array_key_exists('relativePath', $arr)) {
                $entity->setRelativePath($arr['relativePath']);
                $fs->copy(__FILE__, $storePath . '/' . $arr['relativePath']);
            }

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
