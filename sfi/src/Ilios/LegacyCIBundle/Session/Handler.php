<?php

namespace Ilios\LegacyCIBundle\Session;

use Doctrine\Common\Persistence\ObjectManager;
use Ilios\CoreBundle\Entity\CISession;
use Ilios\LegacyCIBundle\Session\Extractor;

/**
 * Handle CodeIgner Sessions
 */
class Handler
{

    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var Doctrine\Common\Persistence\ObjectRepository 
     */
    private $repository;

    /**
     * @var Ilios\LegacyCIBundle\Session\Extrctor
     */
    private $extractor;
    // session config
    private $sessionCookieName;
    private $encryptionKey;

    /**
     * @param ObjectManager $om
     * @param string $entityClass
     * @param Extractor
     */
    public function __construct(ObjectManager $om, $entityClass, Extractor $extractor)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository($entityClass);
        $this->extractor = $extractor;
    }

    /**
     * @return bool
     */
    public function getUserId()
    {
        return $this->get('uid');
    }

    /**
     * Retrieves a value from the user session by its given key.
     * 
     * @param string $key
     * 
     * @return mixed The value or FALSE if non was found.
     */
    protected function get($key)
    {
        $entity = $this->getCISession();

        if ($entity) {
            return $entity->getUserDataItem($key);
        }

        return false;
    }

    /**
     * Get the Code Ignter Session from the database
     * 
     * @return Ilios\CoreBundle\Entity\CISession
     */
    protected function getCISession()
    {
        $sessionid = $this->extractor->getSessionId();

        return $this->repository->findOneBy(array('sessionId' => $sessionid));
    }
}
