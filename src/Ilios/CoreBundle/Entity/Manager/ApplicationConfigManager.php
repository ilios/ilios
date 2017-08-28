<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Repository\ApplicationConfigRepository;

/**
 * Class ApplicationConfigManager
 * @package Ilios\CoreBundle\Entity\Manager
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
