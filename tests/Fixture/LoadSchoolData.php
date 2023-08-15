<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\School;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSchoolData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('App\Tests\DataLoader\SchoolData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(School::class);
        foreach ($data as $arr) {
            $entity = new School();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            if (array_key_exists('templatePrefix', $arr)) {
                $entity->setTemplatePrefix($arr['templatePrefix']);
            }
            $entity->setIliosAdministratorEmail($arr['iliosAdministratorEmail']);
            $entity->setChangeAlertRecipients($arr['changeAlertRecipients']);
            $repository->update($entity, true, true);
            $this->addReference('schools' . $arr['id'], $entity);
        }
        $repository->flush();
    }
}
