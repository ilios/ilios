<?php
namespace Ilios\CoreBundle\Service;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\ServerException;
use Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager;
use function Stringy\create as s;

class Config
{
    const BOOLEAN_NAMES = [
        'keep_frontend_updated',
        'cas_authentication_verify_ssl',
        'enable_tracking',
        'requireSecureConnection',
    ];

    /**
     * @var ApplicationConfigManager
     */
    protected $applicationConfigManager;

    /**
     * Config constructor.
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
     * Look in ENV varialbes first, if this is set there then
     * go ahead and ignore the DB
     *
     * @param $name
     *
     * @return string | null
     */
    public function get($name)
    {
        $result = $this->getValueFromEnv($name);
        if (null === $result) {
            $result = $this->getValueFromDb($name);
        }

        return $this->castResult($name, $result);
    }

    /**
     * See if there is an environmental variable for this var
     *
     * @param $name
     * @return string | null
     */
    protected function getValueFromEnv($name)
    {
        $envName = 'ILIOS_' .  s($name)->underscored()->toUpperCase();
        if (isset($_SERVER[$envName])) {
            $result = $_SERVER[$envName];
            $lowerCaseResult = strtolower($result);
            if (in_array($lowerCaseResult, ['null', 'false', 'true'])) {
                $result = json_decode($lowerCaseResult);
            }
            return $result;
        }

        return null;
    }

    /**
     * Get the value from the database.
     * If there is a problem connecting to the DB or with the tables we
     * just return null
     *
     * @param $name
     * @return string | null
     */
    protected function getValueFromDb($name)
    {
        try {
            return $this->applicationConfigManager->getValue($name);
        } catch (ServerException $e) {
            return null;
        } catch (ConnectionException $e) {
            return null;
        }
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
            return (boolean) json_decode($result);
        }

        return $result;
    }
}
