<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SessionType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_3
 */
class SessionTypeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'sessionTypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadAssessmentOptionData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadProgramData',
            'Tests\CoreBundle\Fixture\LoadVocabularyData',
            'Tests\CoreBundle\Fixture\LoadTermData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(100)],
            'assessmentOption' => ['assessmentOption', '2'],
            'school' => ['school', '2'],
            'aamcMethods' => ['aamcMethods', ['AM002']],
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
            'title' => [[1], ['title' => 'second session type']],
            'assessmentOption' => [[1], ['assessmentOption' => '2']],
            'school' => [[0, 1], ['school' => 1]],
            'schools' => [[0, 1], ['school' => [1]]],
            'aamcMethods' => [[0, 1], ['aamcMethods' => ['AM001']], $skipped = true],
            'sessions' => [[1], ['sessions' => [2]]],
            'courses' => [[0, 1], ['courses' => [1, 2]]],
            'learningMaterials' => [[0, 1], ['learningMaterials' => [1, 2, 3]]],
            'instructors' => [[0, 1], ['instructors' => [2]]],
            'programs' => [[0, 1], ['programs' => [1, 2]]],
            'instructorGroups' => [[1], ['instructorGroups' => [2]]],
            'competencies' => [[0, 1], ['competencies' => [1]]],
            'meshDescriptors' => [[1], ['meshDescriptors' => ['abc2', 'abc3']]],
            'terms' => [[0 , 1], ['terms' => [1, 2]]],
        ];
    }

    protected function compareData(array $expected, array $result)
    {
        unset($expected['sessions']);
        return parent::compareData($expected, $result);
    }
}
