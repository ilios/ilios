<?php

namespace Tests\AppBundle\Fixture;

use AppBundle\Entity\AuditLog;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadAuditLogData
 */
class LoadAuditLogData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('app.dataloader.auditlog')
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
