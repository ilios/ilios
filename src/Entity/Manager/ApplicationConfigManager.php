<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\Repository\ApplicationConfigRepository;
use Exception;

/**
 * Class ApplicationConfigManager
 * @package App\Entity\Manager
 */
class ApplicationConfigManager extends BaseManager
{
    /**
     * @return array
     * @throws Exception
     */
    protected function getCache(): array
    {
        static $cache;
        if (! isset($cache)) {
            $cache = [];

            /** @var ApplicationConfigRepository $repository */
            $repository = $this->getRepository();
            $configs = $repository->getAllValues();

            foreach ($configs as ['name' => $name, 'value' => $value]) {
                $cache[$name] = $value;
            }
        }
        return $cache;
    }

    /**
     * @param $name
     * @return mixed|null
     * @throws Exception
     */
    public function getValue($name)
    {
        $cache = $this->getCache();
        if (array_key_exists($name, $cache)) {
            return $cache[$name];
        }

        return null;
    }
}
