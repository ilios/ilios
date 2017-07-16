<?php
namespace Ilios\CoreBundle\Service;


use Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager;

class ApplicationConfiguration
{
    const BOOLEAN_NAMES = [
        'keep_frontend_updated',
        'cas_authentication_verify_ssl',
        'enable_tracking',
        'keep_frontend_updated',
    ];

    /**
     * @var ApplicationConfigManager
     */
    protected $applicationConfigManager;

    /**
     * ApplicationConfiguration constructor.
     * @param string $kernelRootDir
     * @param ApplicationConfigManager $applicationConfigManager
     *
     * @throws \Exception
     */
    public function __construct(
        ApplicationConfigManager $applicationConfigManager
    ) {
        $this->applicationConfigManager = $applicationConfigManager;
    }

    /**
     * @param $name
     *
     * @return string | null
     */
    public function get($name)
    {
        $result = $this->applicationConfigManager->getValue($name);

        return $this->castResult($name, $result);
    }

    /**
     * Some parameters are expected to be booleans because they always have been.
     * Since the database stores all of these as long_text we need to cast them back
     *
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    protected function castResult($name, $result)
    {
        if (null !== $result && in_array($name, self::BOOLEAN_NAMES)) {
            return (boolean) $result;
        }

        return $result;
    }
}