<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * School API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class SchoolTest extends AbstractTest
{
    protected $testName =  'school';

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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'title' => [[0], ['filters[title]' => 'test']],
            'iliosAdministratorEmail' => [[0], ['filters[iliosAdministratorEmail]' => 'test']],
            'changeAlertRecipients' => [[0], ['filters[changeAlertRecipients]' => 'test']],
            'competencies' => [[0], ['filters[competencies]' => [1]]],
            'courses' => [[0], ['filters[courses]' => [1]]],
            'programs' => [[0], ['filters[programs]' => [1]]],
            'departments' => [[0], ['filters[departments]' => [1]]],
            'vocabularies' => [[0], ['filters[vocabularies]' => [1]]],
            'instructorGroups' => [[0], ['filters[instructorGroups]' => [1]]],
            'curriculumInventoryInstitution' => [[0], ['filters[curriculumInventoryInstitution]' => 'test']],
            'sessionTypes' => [[0], ['filters[sessionTypes]' => [1]]],
            'stewards' => [[0], ['filters[stewards]' => [1]]],
            'directors' => [[0], ['filters[directors]' => [1]]],
            'administrators' => [[0], ['filters[administrators]' => [1]]],
            'configurations' => [[0], ['filters[configurations]' => [1]]],
        ];
    }

}