<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class AamcMethodManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcMethodManager extends BaseManager implements AamcMethodManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAamcMethodBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findAamcMethodsBy(
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
    public function updateAamcMethod(
        AamcMethodInterface $aamcMethod,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($aamcMethod);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($aamcMethod));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAamcMethod(
        AamcMethodInterface $aamcMethod
    ) {
        $this->em->remove($aamcMethod);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAamcMethod()
    {
        $class = $this->getClass();
        return new $class();
    }
}
