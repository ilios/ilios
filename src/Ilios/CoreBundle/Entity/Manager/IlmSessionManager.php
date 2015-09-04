<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IlmSessionManager extends AbstractManager implements IlmSessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IlmSessionInterface
     */
    public function findIlmSessionBy(
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
     * @return ArrayCollection|IlmSessionInterface[]
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
     * @param IlmSessionInterface $ilmSession
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param IlmSessionInterface $ilmSession
     */
    public function deleteIlmSession(
        IlmSessionInterface $ilmSession
    ) {
        $this->em->remove($ilmSession);
        $this->em->flush();
    }

    /**
     * @return IlmSessionInterface
     */
    public function createIlmSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
