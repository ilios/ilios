<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Class AamcPcrsManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcPcrsManager extends BaseManager implements AamcPcrsManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAamcPcrsBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findAamcPcrsesBy(
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
    public function updateAamcPcrs(
        AamcPcrsInterface $aamcPcrs,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($aamcPcrs);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($aamcPcrs));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAamcPcrs(
        AamcPcrsInterface $aamcPcrs
    ) {
        $this->em->remove($aamcPcrs);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAamcPcrs()
    {
        $class = $this->getClass();
        return new $class();
    }
}
