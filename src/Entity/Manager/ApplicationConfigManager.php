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
    protected function getValues(): array
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
        $values = $this->getValues();
        if (array_key_exists($name, $values)) {
            return $values[$name];
        }

        return null;
    }
}
