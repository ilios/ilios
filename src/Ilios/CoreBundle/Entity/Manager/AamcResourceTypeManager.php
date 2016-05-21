<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;

/**
 * Class AamcResourceTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcResourceTypeManager extends AbstractManager implements AamcResourceTypeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAamcResourceTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findAamcResourceTypesBy(
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
    public function updateAamcResourceType(
        AamcResourceTypeInterface $aamcPcrs,
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
    public function deleteAamcResourceType(
        AamcResourceTypeInterface $aamcPcrs
    ) {
        $this->em->remove($aamcPcrs);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAamcResourceType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
