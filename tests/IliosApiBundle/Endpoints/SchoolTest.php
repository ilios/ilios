<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * School API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class SchoolTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'schools';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadSchoolConfigData',
            'Tests\CoreBundle\Fixture\LoadAlertData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
            'Tests\CoreBundle\Fixture\LoadDepartmentData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryInstitutionData',
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadReportData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'iliosAdministratorEmail' => ['iliosAdministratorEmail', $this->getFaker()->email],
            'title' => ['title', $this->getFaker()->text(60)],
            'changeAlertRecipients' => ['changeAlertRecipients', $this->getFaker()->email],
            'competencies' => ['competencies', [1], $skipped = true],
            'courses' => ['courses', [1], $skipped = true],
            'programs' => ['programs', [1], $skipped = true],
            'departments' => ['departments', [1], $skipped = true],
            'vocabularies' => ['vocabularies', [2], $skipped = true],
            'instructorGroups' => ['instructorGroups', [1], $skipped = true],
            'curriculumInventoryInstitution' => ['curriculumInventoryInstitution', 3, $skipped = true],
            'sessionTypes' => ['sessionTypes', [1], $skipped = true],
            'stewards' => ['stewards', [1], $skipped = true],
            'directors' => ['directors', [2]],
            'administrators' => ['administrators', [2]],
            'configurations' => ['configurations', [1], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[2], ['title' => 'third school']],
            'iliosAdministratorEmail' => [[1], ['iliosAdministratorEmail' => 'info@example.com']],
            'changeAlertRecipients' => [[2], ['changeAlertRecipients' => 'info@example.com']],
            'competencies' => [[0], ['competencies' => [1]], $skipped = true],
            'courses' => [[0], ['courses' => [1]], $skipped = true],
            'programs' => [[0], ['programs' => [1]], $skipped = true],
            'departments' => [[0], ['departments' => [1]], $skipped = true],
            'vocabularies' => [[0], ['vocabularies' => [1]], $skipped = true],
            'instructorGroups' => [[0], ['instructorGroups' => [1]], $skipped = true],
            'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test'], $skipped = true],
            'sessionTypes' => [[0], ['sessionTypes' => [1]], $skipped = true],
            'stewards' => [[0], ['stewards' => [1]], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'administrators' => [[0], ['administrators' => [1]], $skipped = true],
            'configurations' => [[0], ['configurations' => [1]], $skipped = true],
        ];
    }

    /**
     * Remove fields not sent by the school endpoint
     * @inheritdoc
     */
    protected function compareData(array $expected, array $result)
    {
        unset($expected['alerts']);
        parent::compareData($expected, $result);
    }
}
