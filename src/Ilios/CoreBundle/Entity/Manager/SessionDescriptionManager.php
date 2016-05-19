<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Class SessionDescriptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionDescriptionManager extends BaseManager implements SessionDescriptionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findSessionDescriptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findSessionDescriptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSessionDescription(
        SessionDescriptionInterface $sessionDescription,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($sessionDescription);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($sessionDescription));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSessionDescription(
        SessionDescriptionInterface $sessionDescription
    ) {
        $this->em->remove($sessionDescription);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createSessionDescription()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSessionDescriptionCount()
    {
        return $this->em->createQuery('SELECT COUNT(s.id) FROM IliosCoreBundle:SessionDescription s')
            ->getSingleScalarResult();
    }
}
