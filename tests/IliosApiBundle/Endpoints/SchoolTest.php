<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * School API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
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
//            'competencies' => ['competencies', [1]],
//            'courses' => ['courses', [1]],
//            'programs' => ['programs', [1]],
//            'departments' => ['departments', [1]],
//            'vocabularies' => ['vocabularies', [2]],
//            'instructorGroups' => ['instructorGroups', [1]],
//            'curriculumInventoryInstitution' => ['curriculumInventoryInstitution', 3],
//            'sessionTypes' => ['sessionTypes', [1]],
//            'stewards' => ['stewards', [1]],
            'directors' => ['directors', [2]],
            'administrators' => ['administrators', [2]],
//            'configurations' => ['configurations', [1]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
//            'competencies' => [[0], ['competencies' => [1]]],
//            'courses' => [[0], ['courses' => [1]]],
//            'programs' => [[0], ['programs' => [1]]],
//            'departments' => [[0], ['departments' => [1]]],
//            'vocabularies' => [[0], ['vocabularies' => [1]]],
//            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
//            'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test']],
//            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
//            'stewards' => [[0], ['stewards' => [1]]],
//            'directors' => [[0], ['directors' => [1]]],
//            'administrators' => [[0], ['administrators' => [1]]],
//            'configurations' => [[0], ['configurations' => [1]]],
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
