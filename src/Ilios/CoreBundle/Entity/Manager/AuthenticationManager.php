<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AuthenticationInterface;

/**
 * Class AuthenticationManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AuthenticationManager extends BaseManager implements AuthenticationManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAuthenticationBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findAuthenticationsBy(
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
    public function findAuthenticationByUsername($username)
    {
        $username = strtolower($username);
        return $this->getRepository()->findOneByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAuthentication(
        AuthenticationInterface $authentication,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($authentication);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($authentication));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAuthentication(
        AuthenticationInterface $authentication
    ) {
        $this->em->remove($authentication);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAuthentication()
    {
        $class = $this->getClass();
        return new $class();
    }
}
