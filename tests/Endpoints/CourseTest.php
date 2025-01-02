<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\DataLoader\IlmSessionData;
use App\Tests\DataLoader\OfferingData;
use App\Tests\DataLoader\SessionData;
use App\Tests\Fixture\LoadCohortData;
use App\Tests\Fixture\LoadCourseClerkshipTypeData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadCourseObjectiveData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\QEndpointTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * Course API endpoint Test.
 */
#[Group('api_2')]
class CourseTest extends AbstractReadWriteEndpoint
{
    use QEndpointTrait;

    protected string $testName =  'courses';

    protected function getFixtures(): array
    {
        return [
            LoadCourseData::class,
            LoadCourseClerkshipTypeData::class,
            LoadSchoolData::class,
            LoadUserData::class,
            LoadCohortData::class,
            LoadTermData::class,
            LoadCourseLearningMaterialData::class,
            LoadSessionData::class,
            LoadOfferingData::class,
            LoadProgramYearData::class,
            LoadSessionLearningMaterialData::class,
            LoadIlmSessionData::class,
            LoadProgramYearObjectiveData::class,
            LoadCourseObjectiveData::class,
            LoadSessionObjectiveData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'icicles are not a snack'],
            'level' => ['level', 3],
            'year' => ['year', 2022],
            'startDate' => ['startDate', '2017-02-14T00:00:00+00:00'],
            'endDate' => ['endDate', '2017-02-14T00:00:00+00:00'],
            'externalId' => ['externalId', 'devnull'],
            'externalIdNull' => ['externalId', null],
            'locked' => ['locked', true],
            'archived' => ['archived', true],
            'publishedAsTbd' => ['publishedAsTbd', true],
            'published' => ['published', false],
            'clerkshipType' => ['clerkshipType', 2],
            'removeClerkshipType' => ['clerkshipType', null],
            'school' => ['school', 2],
            'directors' => ['directors', [2]],
            'removeDirectors' => ['directors', []],
            'replaceDirectors' => ['directors', [4]],
            'administrators' => ['administrators', [2]],
            'studentAdvisors' => ['studentAdvisors', [1]],
            'cohorts' => ['cohorts', [2]],
            'terms' => ['terms', [2]],
            'courseObjectives' => ['courseObjectives', [1]],
            'meshDescriptors' => ['meshDescriptors', ['abc3']],
            // 'learningMaterials' => ['learningMaterials', [1]], // skipped
            // 'sessions' => ['sessions', [1]], // skipped
            'ancestor' => ['ancestor', 2],
            // 'descendants' => ['descendants', ['3']], // skipped
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'title' => [[0], ['title' => 'firstCourse']],
            'level' => [[3, 4], ['level' => 3]],
            'year' => [[1, 2], ['year' => 2012]],
            'academicYears' => [[1, 2, 3, 4], ['academicYears' => [2012, 2013]]],
            'academicYear' => [[1, 2], ['academicYears' => 2012]],
            // 'startDate' => [[1], ['startDate' => '2013-09-01T00:00:00+00:00']], // skipped
            // 'endDate' => [[2], ['endDate' => '2013-12-14T00:00:00+00:00']], // skipped
            'externalId' => [[2], ['externalId' => 'course3']],
            'locked' => [[4], ['locked' => true]],
            'archived' => [[4], ['archived' => true]],
            'publishedAsTbd' => [[4], ['publishedAsTbd' => true]],
            'published' => [[0, 4], ['published' => true]],
            'clerkshipType' => [[0, 1], ['clerkshipType' => 1]],
            'school' => [[2, 3, 4], ['school' => 2]],
            'schools' => [[2, 3, 4], ['schools' => [2]]],
            // 'directors' => [[1, 3], ['directors' => [2]]], // skipped
            // 'administrators' => [[0], ['administrators' => [1]]], // skipped
            // 'cohorts' => [[2], ['cohorts' => [2]]], // skipped
            'terms' => [[0, 1], ['terms' => [1]]],
            'meshDescriptors' => [[0, 1, 3], ['meshDescriptors' => ['abc1', 'abc2']]],
            'learningMaterials' => [[0, 1, 3], ['learningMaterials' => [1, 3]]],
            'sessions' => [[1], ['sessions' => [3]]],
            'ancestor' => [[3], ['ancestor' => 3]],
            'ancestors' => [[3], ['ancestors' => [3]]],
            // 'descendants' => [[0], ['descendants' => [1]]], // skipped
            'programs' => [[3, 4], ['programs' => [2]]],
            'instructors' => [[0, 1, 3], ['instructors' => [1, 2]]],
            'instructorGroups' => [[0, 1], ['instructorGroups' => [1]]],
            'programYears' => [[2], ['programYears' => [2]]],
            'competencies' => [[0], ['competencies' => [1]]],
            'yearAndLevel' => [[3, 4], ['level' => 3, 'year' => 2013]],
            'yearAndSchool' => [[3, 4], ['school' => 2, 'year' => 2013]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];

        return $filters;
    }

    public function testGetMyCourses(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $this->filterTest(
            ['my' => true],
            [$all[0], $all[1], $all[3]],
            $jwt
        );
    }

    public function testGetMyCoursesSorted(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $this->filterTest(
            ['my' => true, 'order_by[year]' => 'ASC', 'order_by[id]' => 'DESC'],
            [$all[1], $all[3], $all[0]],
            $jwt
        );
    }

    public function testGetMyCoursesFailureOnBogusOrderBy(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->badFilterTest(
            ['my' => true, 'order_by[glefarknik]' => 'ASC'],
            $jwt
        );
    }

    public function testGetMyCoursesFailureOnBogusFilterBy(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->badFilterTest(
            ['my' => true, 'filters[farnk]' => 1],
            $jwt
        );
    }

    public function testGetMyCoursesWithLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $this->filterTest(
            ['my' => true, 'limit' => 2],
            [$all[0], $all[1]],
            $jwt
        );
    }

    public function testGetMyCoursesWithLimitAndOffset(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $this->filterTest(
            ['my' => true, 'limit' => 1, 'offset' => 1],
            [$all[1]],
            $jwt
        );
    }

    public function testGetMyCoursesFilteredByYear(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $this->filterTest(
            ['my' => true, 'filters[year]' => '2012'],
            [$all[1]],
            $jwt
        );

        $this->filterTest(
            ['my' => true, 'filters[year]' => '2013'],
            [$all[3]],
            $jwt
        );

        $this->filterTest(
            ['my' => true, 'filters[year]' => ['2012', '2013']],
            [$all[1], $all[3]],
            $jwt
        );
    }

    /**
     * Ember doesn't send the non-owning side of many-to-one relationships
     */
    public function testPutCourseWithoutSessionsAndLms(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();

        $postData = $data;
        unset($postData['sessions']);
        unset($postData['learningMaterials']);

        $this->putTest($data, $postData, $data['id'], $jwt);
    }

    public function testRolloverCourse(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();

        $newCourse = $this->rolloverCourse([
            'id' => $course['id'],
            'year' => 2024,
            'newStartDate' => 'false',
            'skipOfferings' => 'false',
        ]);

        $this->assertSame($course['title'], $newCourse['title']);
        $this->assertSame($course['level'], $newCourse['level']);
        $this->assertSame($course['externalId'], $newCourse['externalId']);
        $this->assertSame(2024, $newCourse['year']);
        $this->assertSame('2024-09-01T00:00:00+00:00', $newCourse['startDate']);
        $this->assertSame('2024-12-29T00:00:00+00:00', $newCourse['endDate']);
        $this->assertFalse($newCourse['locked']);
        $this->assertFalse($newCourse['archived']);
        $this->assertFalse($newCourse['published']);
        $this->assertFalse($newCourse['publishedAsTbd']);

        $this->assertEquals($course['clerkshipType'], $newCourse['clerkshipType']);
        $this->assertEquals($course['school'], $newCourse['school']);
        $this->assertEquals($course['directors'], $newCourse['directors']);
        $this->assertEmpty($newCourse['cohorts']);
        $this->assertEquals($course['terms'], $newCourse['terms']);
        $this->assertSame(count($course['courseObjectives']), count($newCourse['courseObjectives']));
        $this->assertEquals($course['meshDescriptors'], $newCourse['meshDescriptors']);
        $this->assertSame(count($course['learningMaterials']), count($newCourse['learningMaterials']));
        $this->assertEquals($course['id'], $newCourse['ancestor']);

        $newSessions = $newCourse['sessions'];
        $this->assertEquals(2, count($newSessions));
        $sessions = self::getContainer()->get(SessionData::class)->getAll();
        $lastSessionId = array_pop($sessions)['id'];

        $this->assertEquals($lastSessionId + 1, $newSessions[0], 'incremented session id 1');
        $this->assertEquals($lastSessionId + 2, $newSessions[1], 'incremented session id 2');

        $newSessionsData = $this->getFiltered('sessions', 'sessions', ['filters[id]' => $newSessions], $jwt);
        $offerings = self::getContainer()->get(OfferingData::class)->getAll();
        $lastOfferingId = array_pop($offerings)['id'];

        $firstSessionOfferings = array_map('strval', [$lastOfferingId + 1, $lastOfferingId + 2]);
        $secondSessionOfferings = array_map('strval', [$lastOfferingId + 3, $lastOfferingId + 4, $lastOfferingId + 5]);

        $this->assertEquals($firstSessionOfferings, $newSessionsData[0]['offerings']);
        $this->assertEquals($secondSessionOfferings, $newSessionsData[1]['offerings']);
    }

    public function testRolloverCourseWithStartDate(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();

        $newCourse = $this->rolloverCourse([
            'id' => $course['id'],
            'year' => 2024,
            'newStartDate' => '2024-02-04',
        ]);

        $this->assertSame(2024, $newCourse['year']);
        $this->assertSame('2024-02-04T00:00:00+00:00', $newCourse['startDate'], 'start date is correct');
        $this->assertSame('2024-06-02T00:00:00+00:00', $newCourse['endDate'], 'end date is correct');

        $newSessions = $newCourse['sessions'];
        $this->assertEquals(2, count($newSessions));

        $newSessionsData = $this->getFiltered('sessions', 'sessions', ['filters[id]' => $newSessions], $jwt);

        $session1Offerings = $newSessionsData[0]['offerings'];
        $session1OfferingData = $this->getFiltered(
            'offerings',
            'offerings',
            ['filters[id]' => $session1Offerings],
            $jwt
        );

        usort($session1OfferingData, fn($a, $b) => strtotime($a['startDate']) - strtotime($b['startDate']));
        $this->assertEquals('2024-02-08T15:00:00+00:00', $session1OfferingData[0]['startDate']);
    }

    public function testRolloverCourseWithNoOfferings(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();

        $newCourse = $this->rolloverCourse([
            'id' => $course['id'],
            'year' => 2030,
            'skipOfferings' => true,
        ]);

        $this->assertSame(2030, $newCourse['year']);
        $newSessions = $newCourse['sessions'];
        $this->assertEquals(2, count($newSessions));
        $sessions = self::getContainer()->get(SessionData::class)->getAll();
        $lastSessionId = array_pop($sessions)['id'];

        $this->assertEquals($lastSessionId + 1, $newSessions[0], 'incremented session id 1');
        $this->assertEquals($lastSessionId + 2, $newSessions[1], 'incremented session id 2');

        $data = $this->getFiltered('sessions', 'sessions', ['filters[id]' => $newSessions], $jwt);

        $this->assertEmpty($data[0]['offerings']);
        $this->assertEmpty($data[1]['offerings']);
    }

    public function testRolloverCourseWithNewTitle(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        /* KLUDGE!
         * Fix up the course's year to be last year's.
         * Otherwise, rollover may bomb out with a "Courses cannot be rolled over to a new year before YYYY" error,
         * with YYYY being last year.
         * [ST 2018/01/02].
         */
        $course['year'] = intval(date('Y')) - 1;
        $newCourseTitle = 'New (very cool) course title';

        $newCourse = $this->rolloverCourse([
            'id' => $course['id'],
            'year' => $course['year'],
            'newCourseTitle' => $newCourseTitle,
        ]);

        $this->assertSame($course['year'], $newCourse['year']);
        $this->assertSame($newCourseTitle, $newCourse['title']);
    }

    public function testFailRolloverToPassedYear(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        $course['year'] = 2001; // most definitely yesteryear
        $newCourseTitle = 'Does not matter';

        $parameters = [
            'version' => $this->apiVersion,
            'id' => $course['id'],
            'year' => $course['year'],
            'newCourseTitle' => $newCourseTitle,
        ];

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_courses_rollover",
                $parameters
            ),
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('Courses cannot be rolled over to a new year before', $data['detail']);
    }

    public function testRolloverIlmSessions(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $course = $all[1];

        $newCourse = $this->rolloverCourse([
            'id' => $course['id'],
            'year' => 2024,
        ]);

        $newSessionIds = $newCourse['sessions'];
        $this->assertEquals(5, count($newSessionIds));

        $newSessionData = $this->getFiltered('sessions', 'sessions', ['filters[id]' => $newSessionIds], $jwt);

        $newSessionsWithILMs = array_filter($newSessionData, fn(array $session) => !empty($session['ilmSession']));
        $this->assertEquals(4, count($newSessionsWithILMs));

        $newIlmIds = array_map(fn(array $session) => $session['ilmSession'], $newSessionsWithILMs);
        $newIlmIds = array_values($newIlmIds);

        $ilms = self::getContainer()->get(IlmSessionData::class)->getAll();
        $lastIlmId = $ilms[key(array_slice($ilms, -1, 1, true))]['id'];

        $this->assertEquals($lastIlmId + 1, $newIlmIds[0], 'incremented ilm id 1');
        $this->assertEquals($lastIlmId + 2, $newIlmIds[1], 'incremented ilm id 2');
        $this->assertEquals($lastIlmId + 3, $newIlmIds[2], 'incremented ilm id 3');
        $this->assertEquals($lastIlmId + 4, $newIlmIds[3], 'incremented ilm id 4');

        $newIlmData = $this->getFiltered('ilmsessions', 'ilmSessions', ['filters[id]' => $newIlmIds], $jwt);

        $this->assertEquals($newIlmData[0]['hours'], $ilms[0]['hours']);
        $this->assertEquals($newIlmData[1]['hours'], $ilms[1]['hours']);
    }

    public function testRolloverCourseWithCohorts(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();

        $newCourse = $this->rolloverCourse([
            'id' => $course['id'],
            'year' => 2024,
            'newStartDate' => 'false',
            'skipOfferings' => 'true',
            'newCohorts' => [5],
        ]);

        $this->assertSame($course['title'], $newCourse['title']);
        $this->assertCount(1, $newCourse['cohorts']);
        $this->assertSame('5', $newCourse['cohorts'][0]);
        $this->assertSame(count($course['courseObjectives']), count($newCourse['courseObjectives']));
        $oldCourseObjectiveData = $this->getFiltered(
            'courseobjectives',
            'courseObjectives',
            ['filters[id]' => $course['courseObjectives']],
            $jwt
        );
        $newCourseObjectivesData = $this->getFiltered(
            'courseobjectives',
            'courseObjectives',
            ['filters[id]' => $newCourse['courseObjectives']],
            $jwt
        );

        $this->assertCount(count($oldCourseObjectiveData), $newCourseObjectivesData);
        $this->assertCount(1, $newCourseObjectivesData[0]['programYearObjectives']);
        $this->assertEquals('2', $newCourseObjectivesData[0]['programYearObjectives'][0]);
    }

    protected function rolloverCourse(array $rolloverDetails): array
    {
        $parameters = array_merge([
            'version' => $this->apiVersion,
        ], $rolloverDetails);

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_courses_rollover",
                $parameters
            ),
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['courses'];

        $this->assertArrayHasKey(0, $data);

        return $data[0];
    }

    public function testRejectUnprivilegedPostCourse(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_courses_post",
                ['version' => $this->apiVersion]
            ),
            json_encode(['courses' => [$course]])
        );
    }

    public function testRejectUnprivilegedPutCourse(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);
        $id = $course['id'];

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_courses_put",
                ['version' => $this->apiVersion, 'id' => $id]
            ),
            json_encode(['course' => $course])
        );
    }

    public function testRejectUnprivilegedPutCourseWithWrongId(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);
        $id = $course['id'];


        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_courses_put",
                ['version' => $this->apiVersion, 'id' => $id * 10000]
            ),
            json_encode(['course' => $course])
        );
    }

    public function testRejectUnprivilegedDeleteCourse(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);
        $id = $course['id'];

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_courses_delete",
                ['version' => $this->apiVersion, 'id' => $id]
            )
        );
    }

    public function testRejectUnprivilegedRollover(): void
    {
        $dataLoader = $this->getDataLoader();
        $course = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);
        $id = $course['id'];

        $rolloverData = [
            'version' => $this->apiVersion,
            'id' => $id,
            'year' => 2023,
            'newStartDate' => 'false',
            'skipOfferings' => 'false',
        ];
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl($this->kernelBrowser, "app_api_courses_rollover", $rolloverData)
        );
    }

    public function testCourseCanBeUnlocked(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['locked'] = true;
        $postData = $data;
        $responseData = $this->putTest($data, $postData, $data['id'], $jwt);
        $this->assertTrue(
            $responseData['locked']
        );

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['locked'] = false;
        $postData = $data;
        $responseData = $this->putTest($data, $postData, $data['id'], $jwt);
        $this->assertFalse(
            $responseData['locked']
        );
    }

    public function testRemovingCourseObjectiveRemovesSessionObjectivesToo(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $courseObjectiveId = $data['courseObjectives'][0];
        $courseObjective = $this->getOne(
            'courseobjectives',
            'courseObjectives',
            $courseObjectiveId,
            $jwt
        );
        $sessionObjectiveId = $courseObjective['sessionObjectives'][0];
        $sessionObjective = $this->getOne(
            'sessionobjectives',
            'sessionObjectives',
            $sessionObjectiveId,
            $jwt
        );
        // session objective is linked to course objective
        $this->assertTrue(in_array($courseObjective['id'], $sessionObjective['courseObjectives']));

        // remove course objective
        $data['courseObjectives'] = [];
        $postData = $data;
        $this->putOne('courses', 'course', $postData['id'], $postData, $jwt);

        // verify that session objective is no longer linked to removed course objective
        $sessionObjective = $this->getOne(
            'sessionobjectives',
            'sessionObjectives',
            $sessionObjectiveId,
            $jwt,
        );
        $this->assertFalse(in_array($courseObjective['id'], $sessionObjective['courseObjectives']));
    }

    public function testPutCourseWithBadSchoolId(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['school'] = 99;

        $this->badPostTest($data, $jwt);
    }

    public function testPutCourseWithBadSessionId(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['sessions'] = [1, 99, 14];

        $this->badPostTest($data, $jwt);
    }

    public function testGetMyCoursesIncludesAdministeredCourses(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 4);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $this->filterTest(
            ['my' => true],
            [$all[0], $all[2], $all[4]],
            $jwt
        );
    }

    public function testIncludeBothProgramYearProgramAndObjectivesWithCohort(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $includes = $this->getJsonApiIncludes(
            'courses',
            '1',
            'cohorts.programYear.program,cohorts.programYear.programYearObjectives',
            $jwt
        );

        $this->assertArrayHasKey('programYears', $includes);
        $this->assertArrayHasKey('programs', $includes);
        $this->assertArrayHasKey('programYearObjectives', $includes);

        $this->assertIsArray($includes['programYears']);
        $this->assertEquals(['1'], $includes['programYears']);
        $this->assertIsArray($includes['programs']);
        $this->assertEquals(['1'], $includes['programs']);
        $this->assertIsArray($includes['programYearObjectives']);
        $this->assertEquals(['1'], $includes['programYearObjectives']);
    }

    public function testIncludeSessionDetails(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $sessionRelationships = [
            'learningMaterials.learningMaterial.owningUser',
            'sessionObjectives.courseObjectives',
            'sessionObjectives.meshDescriptors',
            'sessionObjectives.terms.vocabulary',
            'offerings.learners',
            'offerings.instructors',
            'offerings.instructorGroups.users',
            'offerings.learnerGroups.users',
            'ilmSession.learners',
            'ilmSession.instructors',
            'ilmSession.instructorGroups.users',
            'ilmSession.learnerGroups.users',
            'terms.vocabulary',
            'meshDescriptors.trees',
        ];
        $sessionIncludes = array_reduce($sessionRelationships, fn($carry, $item) => "{$carry}sessions.$item,", '');

        $includes = $this->getJsonApiIncludes('courses', '1', $sessionIncludes, $jwt);

        $this->assertArrayHasKey('sessions', $includes);
        $this->assertArrayHasKey('terms', $includes);
        $this->assertArrayHasKey('vocabularies', $includes);
        $this->assertArrayHasKey('sessionObjectives', $includes);
        $this->assertArrayHasKey('courseObjectives', $includes);
        $this->assertArrayHasKey('meshDescriptors', $includes);
        $this->assertArrayHasKey('sessionLearningMaterials', $includes);
        $this->assertArrayHasKey('learningMaterials', $includes);
        $this->assertArrayHasKey('users', $includes);
        $this->assertArrayHasKey('offerings', $includes);
        $this->assertArrayHasKey('learnerGroups', $includes);
        $this->assertArrayHasKey('instructorGroups', $includes);

        $this->assertIsArray($includes['sessions']);
        $this->assertEquals(['1', '2'], $includes['sessions']);
        $this->assertIsArray($includes['terms']);
        $this->assertEquals(['1', '2', '3', '4', '5'], $includes['terms']);
        $this->assertIsArray($includes['vocabularies']);
        $this->assertEquals(['1', '2'], $includes['vocabularies']);
        $this->assertIsArray($includes['sessionObjectives']);
        $this->assertEquals(['1'], $includes['sessionObjectives']);
        $this->assertIsArray($includes['courseObjectives']);
        $this->assertEquals(['1'], $includes['courseObjectives']);
        $this->assertIsArray($includes['meshDescriptors']);
        $this->assertEquals(['abc1', 'abc2'], $includes['meshDescriptors']);
        $this->assertIsArray($includes['sessionLearningMaterials']);
        $this->assertEquals(['1', '9'], $includes['sessionLearningMaterials']);
        $this->assertIsArray($includes['learningMaterials']);
        $this->assertEquals(['1', '10'], $includes['learningMaterials']);
        $this->assertIsArray($includes['users']);
        $this->assertEquals(['1', '2', '4', '5'], $includes['users']);
        $this->assertIsArray($includes['offerings']);
        $this->assertEquals(['1', '2', '3', '4', '5'], $includes['offerings']);
        $this->assertIsArray($includes['learnerGroups']);
        $this->assertEquals(['1', '2', '5'], $includes['learnerGroups']);
        $this->assertIsArray($includes['instructorGroups']);
        $this->assertEquals(['1', '2'], $includes['instructorGroups']);
    }

    public function testIncludedDataNotLoadedForUnprivilegedUsers(): void
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_courses_getone",
            [
                'version' => $this->apiVersion,
                'id' => 1,
                'include' => 'sessions.administrators',
            ]
        );
        $this->createJsonApiRequest(
            'GET',
            $url,
            null,
            $this->createJwtFromUserId($this->kernelBrowser, 5)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent());
        $this->assertTrue(property_exists($content, 'included'));

        $types = array_column($content->included, 'type');
        $this->assertCount(2, $content->included);
        $this->assertNotContains('users', $types);
    }

    public function testGraphQLIncludedData(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();

        $this->createGraphQLRequest(
            json_encode([
                'query' =>
                    "query { courses(id: {$data['id']}) { id, school { id }, sessions { id, administrators { id }} }}",
            ]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertGraphQLResponse($response);

        $content = json_decode($response->getContent());

        $this->assertIsObject($content->data);
        $this->assertIsArray($content->data->courses);

        $result = $content->data->courses;
        $this->assertCount(1, $result);

        $course = $result[0];
        $this->assertTrue(property_exists($course, 'id'));
        $this->assertEquals($data['id'], $course->id);
        $this->assertTrue(property_exists($course, 'school'));
        $this->assertTrue(property_exists($course->school, 'id'));
        $this->assertEquals($data['school'], $course->school->id);
        $this->assertCount(2, $course->sessions);

        $this->assertTrue(property_exists($course->sessions[0], 'id'));
        $this->assertEquals(1, $course->sessions[0]->id);
        $this->assertTrue(property_exists($course->sessions[0], 'administrators'));
        $this->assertCount(1, $course->sessions[0]->administrators);
        $this->assertTrue(property_exists($course->sessions[0]->administrators[0], 'id'));
        $this->assertEquals(1, $course->sessions[0]->administrators[0]->id);

        $this->assertTrue(property_exists($course->sessions[1], 'id'));
        $this->assertEquals(2, $course->sessions[1]->id);
        $this->assertTrue(property_exists($course->sessions[1], 'administrators'));
        $this->assertCount(0, $course->sessions[1]->administrators);
    }

    public function testGraphQLIncludedDataNotLoadedForUnprivilegedUsers(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();

        $this->createGraphQLRequest(
            json_encode([
                'query' => "query { courses(id: {$data['id']}) { id, sessions { id, administrators { id }} }}",
            ]),
            $this->createJwtFromUserId($this->kernelBrowser, 5)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertGraphQLResponse($response);

        $content = json_decode($response->getContent());

        $this->assertIsObject($content->data);
        $this->assertIsArray($content->data->courses);

        $result = $content->data->courses;
        $this->assertCount(1, $result);

        $course = $result[0];
        $this->assertTrue(property_exists($course, 'id'));
        $this->assertEquals($data['id'], $course->id);
        $this->assertCount(2, $course->sessions);

        $this->assertTrue(property_exists($course->sessions[0], 'id'));
        $this->assertEquals(1, $course->sessions[0]->id);
        $this->assertTrue(property_exists($course->sessions[0], 'administrators'));
        $this->assertCount(0, $course->sessions[0]->administrators);

        $this->assertTrue(property_exists($course->sessions[1], 'id'));
        $this->assertEquals(2, $course->sessions[1]->id);
        $this->assertTrue(property_exists($course->sessions[1], 'administrators'));
        $this->assertCount(0, $course->sessions[1]->administrators);
    }


    public static function qsToTest(): array
    {
        return [
            ['first', [0]],
            ['ours', [0, 1, 2, 3, 4]],
            ['fourth', [3]],
            ['firstCourse', [0]],
            ['2012', [1, 2]],
            ['second', [1]],
        ];
    }

    public function testFindByQWithLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'first', 'limit' => 1];
        $this->filterTest($filters, [$all[0]], $jwt);
        $filters = ['q' => 'course', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]], $jwt);
    }

    public function testFindByQWithOffset(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'course', 'offset' => 2];
        $this->filterTest($filters, [$all[2], $all[3], $all[4]], $jwt);
    }

    public function testFindByQWithOffsetAndLimit(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'course', 'offset' => 2, 'limit' => 1];
        $this->filterTest($filters, [$all[2]], $jwt);
        $filters = ['q' => 'course', 'offset' => 1, 'limit' => 2];
        $this->filterTest($filters, [$all[1], $all[2]], $jwt);
    }

    public function testFindByQWithOffsetAndLimitJsonApi(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => 'course', 'offset' => 2, 'limit' => 1];
        $this->filterTest($filters, [$all[2]], $jwt);
        $filters = ['q' => 'course', 'offset' => 1, 'limit' => 2];
        $this->filterTest($filters, [$all[1], $all[2]], $jwt);
    }
}
