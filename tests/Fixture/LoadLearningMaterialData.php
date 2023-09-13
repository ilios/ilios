<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearningMaterial;
use App\Entity\LearningMaterialStatus;
use App\Entity\LearningMaterialUserRole;
use App\Entity\User;
use App\Service\Config;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Tests\DataLoader\LearningMaterialData;

class LoadLearningMaterialData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public const TEST_FILE_PATH = __DIR__ . '/FakeTestFiles/TESTFILE.txt';

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get(LearningMaterialData::class)
            ->getAll();

        $fs = new Filesystem();
        if (!$fs->exists(dirname(self::TEST_FILE_PATH))) {
            $fs->mkdir(dirname(self::TEST_FILE_PATH));
        }
        $fs->copy(__FILE__, self::TEST_FILE_PATH);
        $config = $this->container->get(Config::class);
        $storePath = $config->get('file_system_storage_path');

        foreach ($data as $arr) {
            $entity = new LearningMaterial();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $entity->setOriginalAuthor($arr['originalAuthor']);
            $entity->setCopyrightRationale($arr['copyrightRationale']);
            $entity->setCopyrightPermission($arr['copyrightPermission']);
            $entity->setUserRole(
                $this->getReference(
                    'learningMaterialUserRoles' . $arr['userRole'],
                    LearningMaterialUserRole::class
                )
            );
            $entity->setStatus(
                $this->getReference(
                    'learningMaterialStatus' . $arr['status'],
                    LearningMaterialStatus::class
                )
            );
            $entity->setOwningUser($this->getReference('users' . $arr['owningUser'], User::class));
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
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadLearningMaterialUserRoleData',
            'App\Tests\Fixture\LoadLearningMaterialStatusData',
            'App\Tests\Fixture\LoadUserData',
        ];
    }
}
