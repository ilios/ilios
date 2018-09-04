<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Entity\Repository\ApplicationConfigRepository;

/**
 * Class ApplicationConfigManager
 * @package AppBundle\Entity\Manager
 */
class ApplicationConfigManager extends BaseManager
{
    public function getValue($name)
    {
        /** @var ApplicationConfigRepository $repository */
        $repository = $this->getRepository();

        return $repository->getValue($name);
    }
}
