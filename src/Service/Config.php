<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ApplicationConfigRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\ServerException;
use Exception;

use function preg_replace;
use function strtoupper;

class Config
{
    private const BOOLEAN_NAMES = [
        'keep_frontend_updated',
        'cas_authentication_verify_ssl',
        'requireSecureConnection',
        'errorCaptureEnabled',
        'learningMaterialsDisabled',
        'academic_year_crosses_calendar_year_boundaries',
        'material_status_enabled',
        'showCampusNameOfRecord',
    ];

    private const INT_NAMES = [
        'cas_authentication_version',
    ];

    /**
     * Config constructor.
     *
     * @throws Exception
     */
    public function __construct(protected ApplicationConfigRepository $applicationConfigRepository)
    {
    }

    /**
     * Look in ENV variables first, if this is set there then
     * go ahead and ignore the DB
     *
     */
    public function get(string $name): string|bool|int|null
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
     */
    protected function getValueFromEnv(string $name): string|bool|null
    {
        $snakeName = $this->camelToSnake($name);
        $envName = 'ILIOS_' .  strtoupper($snakeName);
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
     */
    protected function getValueFromDb(string $name): string|null
    {
        try {
            return $this->applicationConfigRepository->getValue($name);
        } catch (ServerException) {
            return null;
        } catch (ConnectionException) {
            return null;
        }
    }

    /**
     * Some parameters are expected to be booleans because they always have been.
     * Since the database stores all of these as long_text we need to cast them back
     *
     */
    protected function castResult(string $name, string|bool|int|null $result): string|bool|int|null
    {
        if (null !== $result && !is_bool($result) && in_array($name, self::BOOLEAN_NAMES)) {
            return (bool) json_decode($result);
        }
        if (null !== $result && !is_int($result) && in_array($name, self::INT_NAMES)) {
            return (int) json_decode($result);
        }

        return $result;
    }

    /**
     * Convert camelCaseString to camel_case_string
     */
    protected function camelToSnake(string $str): string
    {
        $rhett = preg_replace('/[A-Z]/', '_$0', $str);
        $rhett = preg_replace('/[-\s]/', '_', $rhett);
        return strtolower($rhett);
    }
}
