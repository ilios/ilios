<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class OfferingManager extends AbstractManager implements OfferingManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOfferingBy(
        array $criteria,
        array $orderBy = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findOfferingsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateOffering(
        OfferingInterface $offering,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($offering);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($offering));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOffering(
        OfferingInterface $offering
    ) {
        $offering->setDeleted(true);
        $this->updateOffering($offering);
    }

    /**
     * {@inheritdoc}
     */
    public function createOffering()
    {
        $class = $this->getClass();
        return new $class();
    }
}
