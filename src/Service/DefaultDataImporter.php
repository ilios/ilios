<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DataImportRepositoryInterface;
use Exception;

/**
 * A service for importing default application data into the database.
 *
 * @package App\Service
 */
class DefaultDataImporter
{
    public const AAMC_METHOD = 'aamc_method';
    public const AAMC_PCRS = 'aamc_pcrs';
    public const AAMC_RESOURCE_TYPE = 'aamc_resource_type';
    public const ALERT_CHANGE_TYPE = 'alert_change_type';
    public const APPLICATION_CONFIG = 'application_config';
    public const ASSESSMENT_OPTION = 'assessment_option';
    public const COMPETENCY = 'competency';
    public const COMPETENCY_X_AAMC_PCRS = 'competency_x_aamc_pcrs';
    public const COURSE_CLERKSHIP_TYPE = 'course_clerkship_type';
    public const CURRICULUM_INVENTORY_INSTITUTION = 'curriculum_inventory_institution';
    public const LEARNING_MATERIAL_STATUS = 'learning_material_status';
    public const LEARNING_MATERIAL_USER_ROLE = 'learning_material_user_role';
    public const SCHOOL = 'school';
    public const SESSION_TYPE = 'session_type';
    public const SESSION_TYPE_X_AAMC_METHOD = 'session_type_x_aamc_method';
    public const TERM = 'term';
    public const TERM_X_AAMC_RESOURCE_TYPE = 'term_x_aamc_resource_type';
    public const USER_ROLE = 'user_role';
    public const VOCABULARY = 'vocabulary';

    public function __construct(public DefaultDataLoader $defaultDataLoader)
    {
    }

    /**
     * @param DataImportRepositoryInterface $repository
     * @param string $type
     * @param array $referenceMap
     * @return array
     * @throws Exception
     */
    public function import(DataImportRepositoryInterface $repository, string $type, array $referenceMap): array
    {
        $rows = $this->defaultDataLoader->load($type);
        foreach ($rows as $row) {
            $referenceMap = $repository->import($row, $type, $referenceMap);
        }
        return $referenceMap;
    }
}
