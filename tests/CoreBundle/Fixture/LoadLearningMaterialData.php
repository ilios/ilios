<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\LearningMaterial;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tests\CoreBundle\DataLoader\LearningMaterialData;

class LoadLearningMaterialData extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get(LearningMaterialData::class)
            ->getAll();

        $fs = new Filesystem();
        $fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($fakeTestFileDir)) {
            $fs->mkdir($fakeTestFileDir);
        }
        $fs->copy(__FILE__, $fakeTestFileDir . '/TESTFILE.txt');
        $config = $this->container->get('Ilios\CoreBundle\Service\Config');
        $storePath = $config->get('file_system_storage_path');

        foreach ($data as $arr) {
            $entity = new LearningMaterial();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $entity->setOriginalAuthor($arr['originalAuthor']);
            $entity->setCopyrightRationale($arr['copyrightRationale']);
            $entity->setCopyrightPermission($arr['copyrightPermission']);
            $entity->setUserRole($this->getReference('learningMaterialUserRoles' . $arr['userRole']));
            $entity->setStatus($this->getReference('learningMaterialStatus' . $arr['status']));
            $entity->setOwningUser($this->getReference('users' . $arr['owningUser']));
            $optional = [
                'mimetype',
                'link',
                'citation',
                'filename',
                'filesize',
            ];
            foreach ($optional as $key) {
                if (array_key_exists($key, $arr)) {
                    $method = 'set' . ucfirst($key);
                    $entity->$method($arr[$key]);
                }
            }
            //copy a test file into the filestore for this file type LM
            if (array_key_exists('filesize', $arr)) {
                $path = $storePath . '/' . 'fakefile' . $arr['id'];
                $entity->setRelativePath('fakefile' . $arr['id']);
                $fs->copy(__FILE__, $path);
            }
            $entity->generateToken();
            $manager->persist($entity);
            $this->addReference('learningMaterials' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadLearningMaterialUserRoleData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialStatusData',
            'Tests\CoreBundle\Fixture\LoadUserData',
        );
    }
}
