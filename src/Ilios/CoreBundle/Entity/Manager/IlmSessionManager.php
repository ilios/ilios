<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IlmSessionManager extends BaseManager implements IlmSessionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findIlmSessionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findIlmSessionsBy(
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
    public function updateIlmSession(
        IlmSessionInterface $ilmSession,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($ilmSession);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($ilmSession));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIlmSession(
        IlmSessionInterface $ilmSession
    ) {
        $this->em->remove($ilmSession);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createIlmSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
