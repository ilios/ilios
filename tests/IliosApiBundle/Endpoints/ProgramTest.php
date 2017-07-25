<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Program API endpoint Test.
 * @group api_1
 */
class ProgramTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'programs';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'shortTitle' => ['shortTitle', $this->getFaker()->text(10)],
            'duration' => ['duration', $this->getFaker()->randomDigit],
            'publishedAsTbd' => ['publishedAsTbd', true],
            'published' => ['published', false],
            'school' => ['school', 3],
            'programYears' => ['programYears', [1], $skipped = true],
            'curriculumInventoryReports' => ['curriculumInventoryReports', [1], $skipped = true],
            'directors' => ['directors', [1, 3]],
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
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'title' => [[1], ['title' => 'second program']],
            'shortTitle' => [[0], ['shortTitle' => 'fp']],
            'duration' => [[1, 2], ['duration' => 4]],
            'publishedAsTbd' => [[1], ['publishedAsTbd' => true]],
            'notPublishedAsTbd' => [[0, 2], ['publishedAsTbd' => false]],
            'published' => [[0], ['published' => true]],
            'notPublished' => [[1, 2], ['published' => false]],
            'school' => [[2], ['school' => 2]],
            'schools' => [[0, 1], ['schools' => 1]],
            'programYears' => [[0], ['programYears' => [1]], $skipped = true],
            'curriculumInventoryReports' => [[0], ['curriculumInventoryReports' => [1]], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'durationAndScheduled' => [[1], ['publishedAsTbd' => true, 'duration' => 4]],
            'durationAndSchool' => [[1], ['school' => 1, 'duration' => 4]],
            'courses' => [[1], ['courses' => [4]]],
            'sessions' => [[0], ['sessions' => [3]]],
            'terms' => [[0], ['terms' => [1]]],
        ];
    }

    /**
     * Delete Program 2 explicitly as Program 1 is linked
     * to School 1.  Since sqlite doesn't cascade this doesn't work
     * @inheritdoc
     */
    public function testDelete()
    {
        $this->deleteTest(2);
    }

    public function testRejectUnprivilegedPostProgram()
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $userId,
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'programs']),
            json_encode(['programs' => [$program]])
        );
    }

    public function testRejectUnprivilegedPutProgram()
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'programs', 'id' => $program['id']]),
            json_encode(['program' => $program])
        );
    }

    public function testRejectUnprivilegedPutNewProgram()
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'programs', 'id' => $program['id'] * 10000]),
            json_encode(['program' => $program])
        );
    }

    public function testRejectUnprivilegedDeleteProgram()
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $userId,
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => 'programs', 'id' => $program['id']])
        );
    }
}
