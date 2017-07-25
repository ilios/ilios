<?php

namespace Tests\CliBundle\Fixture;

use Ilios\CoreBundle\Entity\AuditLog;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadAuditLogData
 */
class LoadAuditLogData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('ilioscli.dataloader.auditlog')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new AuditLog();
            $entity->setObjectId($arr['objectId']);
            $entity->setObjectClass($arr['objectClass']);
            $entity->setValuesChanged($arr['valuesChanged']);
            $entity->setCreatedAt($arr['createdAt']);
            $entity->setAction($arr['action']);
            $manager->persist($entity);
        }
        $manager->flush();
    }
}
