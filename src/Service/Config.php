<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ApplicationConfigRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\ServerException;

use function Stringy\create as s;

class Config
{
    private const BOOLEAN_NAMES = [
        'keep_frontend_updated',
        'cas_authentication_verify_ssl',
        'enable_tracking',
        'requireSecureConnection',
        'errorCaptureEnabled',
        'learningMaterialsDisabled',
        'academic_year_crosses_calendar_year_boundaries'
    ];

    protected ApplicationConfigRepository $applicationConfigRepository;

    /**
     * Config constructor.
     *
     * @throws \Exception
     */
    public function __construct(
        ApplicationConfigRepository $applicationConfigRepository
    ) {
        $this->applicationConfigRepository = $applicationConfigRepository;
    }

    /**
     * Look in ENV variables first, if this is set there then
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
        $result = null;
        if (isset($_ENV[$envName])) {
            $result = $_ENV[$envName];
        }
        if ($result === null && isset($_SERVER[$envName])) {
            $result = $_SERVER[$envName];
        }
        if (is_bool($result)) {
            return $result;
        }
        if ($result !== null) {
            $lowerCaseResult = strtolower($result);
            if (in_array($lowerCaseResult, ['null', 'false', 'true'])) {
                $result = json_decode($lowerCaseResult);
            }
        }

        return $result;
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
            return $this->applicationConfigRepository->getValue($name);
        } catch (ServerException $e) {
            return null;
        } catch (ConnectionException) {
            return null;
        }
    }

    /**
     * Some parameters are expected to be booleans because they always have been.
     * Since the database stores all of these as long_text we need to cast them back
     *
     * @param $name
     * @param $result
     *
     * @return mixed
     */
    protected function castResult($name, $result)
    {
        if (null !== $result && !is_bool($result) && in_array($name, self::BOOLEAN_NAMES)) {
            return (bool) json_decode($result);
        }

        return $result;
    }
}
