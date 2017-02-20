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
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'iliosAdministratorEmail' => ['iliosAdministratorEmail', $this->getFaker()->text],
            'changeAlertRecipients' => ['changeAlertRecipients', $this->getFaker()->text],
            'competencies' => ['competencies', [1]],
            'courses' => ['courses', [1]],
            'programs' => ['programs', [1]],
            'departments' => ['departments', [1]],
            'vocabularies' => ['vocabularies', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'curriculumInventoryInstitution' => ['curriculumInventoryInstitution', $this->getFaker()->text],
            'sessionTypes' => ['sessionTypes', [1]],
            'stewards' => ['stewards', [1]],
            'directors' => ['directors', [1]],
            'administrators' => ['administrators', [1]],
            'configurations' => ['configurations', [1]],
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
            'title' => [[0], ['title' => 'test']],
            'iliosAdministratorEmail' => [[0], ['iliosAdministratorEmail' => 'test']],
            'changeAlertRecipients' => [[0], ['changeAlertRecipients' => 'test']],
            'competencies' => [[0], ['competencies' => [1]]],
            'courses' => [[0], ['courses' => [1]]],
            'programs' => [[0], ['programs' => [1]]],
            'departments' => [[0], ['departments' => [1]]],
            'vocabularies' => [[0], ['vocabularies' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
            'stewards' => [[0], ['stewards' => [1]]],
            'directors' => [[0], ['directors' => [1]]],
            'administrators' => [[0], ['administrators' => [1]]],
            'configurations' => [[0], ['configurations' => [1]]],
        ];
    }

}