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

    /**
     * Config constructor.
     *
     * @throws \Exception
     */
    public function __construct(protected ApplicationConfigRepository $applicationConfigRepository)
    {
    }

    /**
     * Look in ENV variables first, if this is set there then
     * go ahead and ignore the DB
     *
     * @param string $name
     */
    public function get($name): string|bool|null
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
     * @param string $name
     */
    protected function getValueFromEnv($name): string|bool|null
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
     * @param string $name
     */
    protected function getValueFromDb($name): string|null
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
     * @param string $name
     * @param string|boolean $result
     */
    protected function castResult($name, $result): mixed
    {
        if (null !== $result && !is_bool($result) && in_array($name, self::BOOLEAN_NAMES)) {
            return (bool) json_decode($result);
        }

        return $result;
    }
}
