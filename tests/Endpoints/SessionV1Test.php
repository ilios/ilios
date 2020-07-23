<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * Session API V1 endpoint Test.
 * @group api_2
 */
class SessionV1Test extends V1ReadEndpointTest
{
    protected $testName =  'sessions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadLearningMaterialStatusData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $sessionData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1Session = $this->getOne($endpoint, $responseKey, $sessionData['id']);
        $v3Session = $this->getOne($endpoint, $responseKey, $sessionData['id'], 'v3');
        $sessionObjective = $this->getOne(
            'sessionobjectives',
            'sessionObjectives',
            $v3Session['sessionObjectives'][0],
            'v3'
        );
        $objective = $this->getFiltered(
            'objectives',
            'objectives',
            ['filters[sessionObjectives]' => $sessionObjective['id']]
        )[0];
        $this->assertEquals($v3Session['id'], $v1Session['id']);
        $this->assertEquals($v3Session['title'], $v1Session['title']);
        $this->assertEquals($v3Session['attireRequired'], $v1Session['attireRequired']);
        $this->assertEquals($v3Session['supplemental'], $v1Session['supplemental']);
        $this->assertEquals($v3Session['publishedAsTbd'], $v1Session['publishedAsTbd']);
        $this->assertEquals($v3Session['published'], $v1Session['published']);
        $this->assertEquals($v3Session['instructionalNotes'], $v1Session['instructionalNotes']);
        $this->assertEquals($v3Session['updatedAt'], $v1Session['updatedAt']);
        $this->assertEquals($v3Session['sessionType'], $v1Session['sessionType']);
        $this->assertEquals($v3Session['course'], $v1Session['course']);
        $this->assertEquals($v3Session['terms'], $v1Session['terms']);
        $this->assertEquals($v3Session['meshDescriptors'], $v1Session['meshDescriptors']);
        $this->assertEquals($v3Session['administrators'], $v1Session['administrators']);
        $this->assertEquals($v3Session['offerings'], $v1Session['offerings']);
        $this->assertEquals($v3Session['prerequisites'], $v1Session['prerequisites']);
        $this->assertEquals(count($v3Session['sessionObjectives']), count($v1Session['objectives']));
        $this->assertEquals($objective['id'], $v1Session['objectives'][0]);
    }
}
