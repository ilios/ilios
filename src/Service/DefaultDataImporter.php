<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DataImportRepositoryInterface;

/**
 * A service for importing default application data into the database.
 *
 * @package App\Service
 */
class DefaultDataImporter
{
    public const string AAMC_METHOD = 'aamc_method';
    public const string AAMC_PCRS = 'aamc_pcrs';
    public const string AAMC_RESOURCE_TYPE = 'aamc_resource_type';
    public const string ALERT_CHANGE_TYPE = 'alert_change_type';
    public const string APPLICATION_CONFIG = 'application_config';
    public const string ASSESSMENT_OPTION = 'assessment_option';
    public const string COMPETENCY = 'competency';
    public const string COMPETENCY_X_AAMC_PCRS = 'competency_x_aamc_pcrs';
    public const string COURSE_CLERKSHIP_TYPE = 'course_clerkship_type';
    public const string CURRICULUM_INVENTORY_INSTITUTION = 'curriculum_inventory_institution';
    public const string LEARNING_MATERIAL_STATUS = 'learning_material_status';
    public const string LEARNING_MATERIAL_USER_ROLE = 'learning_material_user_role';
    public const string SCHOOL = 'school';
    public const string SESSION_TYPE = 'session_type';
    public const string SESSION_TYPE_X_AAMC_METHOD = 'session_type_x_aamc_method';
    public const string TERM = 'term';
    public const string TERM_X_AAMC_RESOURCE_TYPE = 'term_x_aamc_resource_type';
    public const string USER_ROLE = 'user_role';
    public const string VOCABULARY = 'vocabulary';

    public function __construct(public DefaultDataLoader $defaultDataLoader)
    {
    }

    public function import(DataImportRepositoryInterface $repository, string $type, array $referenceMap): array
    {
        $rows = $this->defaultDataLoader->load($type);
        foreach ($rows as $row) {
            $data = $this->prepareForImport($row, $type);
            $referenceMap = $repository->import($data, $type, $referenceMap);
        }
        return $referenceMap;
    }

    /**
     * Preprocesses a give row of raw CSV data before it's handed to the DB layer for ingestion.
     * Here, we do some mild data normalization as applicable, such as type casting on attributes.
     */
    protected function prepareForImport(array $row, string $type): array
    {
        return match ($type) {
            self::AAMC_METHOD => $this->castToBoolean($row, 2),
            self::ALERT_CHANGE_TYPE,
            self::APPLICATION_CONFIG,
            self::ASSESSMENT_OPTION,
            self::COURSE_CLERKSHIP_TYPE,
            self::LEARNING_MATERIAL_STATUS,
            self::LEARNING_MATERIAL_USER_ROLE,
            self::SCHOOL,
            self::USER_ROLE => $this->castToInteger($row, 0),
            self::COMPETENCY => $this->castToBoolean($this->castToInteger($row, 0), 4),
            self::CURRICULUM_INVENTORY_INSTITUTION => $this->castToInteger($row, 8),
            self::SESSION_TYPE => $this->castToBoolean($this->castToBoolean($this->castToInteger($row, 0), 4), 6),
            self::TERM => $this->castToBoolean($this->castToInteger($row, 0), 5),
            self::VOCABULARY => $this->castToBoolean($this->castToInteger($row, 0), 3),
            default => $row,
        };
    }

    /**
     * Casts the value of a given array item to integer, then returns the modified array.
     */
    protected function castToInteger(array $row, int $index): array
    {
        $row[$index] = (int) $row[$index];
        return $row;
    }

    /**
     * Casts the value of a given array item to boolean, then returns the modified array.
     */
    protected function castToBoolean(array $row, int $index): array
    {
        $row[$index] = (bool) $row[$index];
        return $row;
    }
}
