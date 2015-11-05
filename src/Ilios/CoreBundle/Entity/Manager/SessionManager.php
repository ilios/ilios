<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionManager extends AbstractManager implements SessionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findSessionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findSessionsBy(
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
    public function updateSession(
        SessionInterface $session,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($session);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($session));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSession(
        SessionInterface $session
    ) {
        $this->em->remove($session);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
