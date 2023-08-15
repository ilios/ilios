<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AuditLog;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
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

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('app.dataloader.auditlog')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(AuditLog::class);
        foreach ($data as $arr) {
            $entity = new AuditLog();
            $entity->setObjectId($arr['objectId']);
            $entity->setObjectClass($arr['objectClass']);
            $entity->setValuesChanged($arr['valuesChanged']);
            $entity->setCreatedAt($arr['createdAt']);
            $entity->setAction($arr['action']);
            $repository->update($entity, false, true);
        }
        $manager->flush();
    }
}
