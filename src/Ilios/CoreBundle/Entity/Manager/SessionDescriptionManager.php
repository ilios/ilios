<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Class SessionDescriptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionDescriptionManager extends AbstractManager implements SessionDescriptionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionDescriptionInterface
     */
    public function findSessionDescriptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|SessionDescriptionInterface[]
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
     * @param SessionDescriptionInterface $sessionDescription
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param SessionDescriptionInterface $sessionDescription
     */
    public function deleteSessionDescription(
        SessionDescriptionInterface $sessionDescription
    ) {
        $this->em->remove($sessionDescription);
        $this->em->flush();
    }

    /**
     * @return SessionDescriptionInterface
     */
    public function createSessionDescription()
    {
        $class = $this->getClass();
        return new $class();
    }
}
