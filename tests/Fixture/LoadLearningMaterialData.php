<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearningMaterial;
use App\Entity\LearningMaterialStatus;
use App\Entity\LearningMaterialUserRole;
use App\Entity\User;
use App\Service\Config;
use App\Tests\DataLoader\LearningMaterialData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

class LoadLearningMaterialData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public const string TEST_FILE_PATH = __DIR__ . '/FakeTestFiles/TESTFILE.txt';

    public function __construct(protected Config $config, protected LearningMaterialData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();

        $fs = new Filesystem();
        if (!$fs->exists(dirname(self::TEST_FILE_PATH))) {
            $fs->mkdir(dirname(self::TEST_FILE_PATH));
        }
        $fs->copy(__FILE__, self::TEST_FILE_PATH);
        $storePath = $this->config->get('file_system_storage_path');

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

    public function getDependencies(): array
    {
        return [
            LoadLearningMaterialUserRoleData::class,
            LoadLearningMaterialStatusData::class,
            LoadUserData::class,
        ];
    }
}
