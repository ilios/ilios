<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionManager extends AbstractManager implements SessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionInterface
     */
    public function findSessionBy(
        array $criteria,
        array $orderBy = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|SessionInterface[]
     */
    public function findSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionInterface $session
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param SessionInterface $session
     */
    public function deleteSession(
        SessionInterface $session
    ) {
        $session->setDeleted(true);
        $this->updateSession($session);
    }

    /**
     * @return SessionInterface
     */
    public function createSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
