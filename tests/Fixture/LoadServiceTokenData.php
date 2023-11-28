<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\ServiceToken;
use App\Tests\DataLoader\ServiceTokenData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServiceTokenData extends AbstractFixture implements
    ORMFixtureInterface,
    ContainerAwareInterface
{
    public const REFERENCE_KEY_PREFIX = 'serviceTokens';
    protected ?ContainerInterface $container;

    public function setContainer(?ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->container
            ->get(ServiceTokenData::class)
            ->getAll();
        foreach ($data as $arr) {
            $entity = new ServiceToken();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $entity->setEnabled($arr['enabled']);
            $entity->setCreatedAt($arr['createdAt']);
            $entity->setExpiresAt($arr['expiresAt']);
            $manager->persist($entity);
            $this->addReference(self::REFERENCE_KEY_PREFIX . $arr['id'], $entity);
            $manager->flush();
        }
    }
}
