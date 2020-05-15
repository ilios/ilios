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
            'App\Tests\Fixture\LoadSessionDescriptionData',
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
        $v2Session = $this->getOne($endpoint, $responseKey, $sessionData['id'], 'v2');
        $sessionObjective = $this->getOne(
            'sessionobjectives',
            'sessionObjectives',
            $v2Session['sessionObjectives'][0],
            'v2'
        );
        $this->assertEquals($v2Session['id'], $v1Session['id']);
        $this->assertEquals($v2Session['title'], $v1Session['title']);
        $this->assertEquals($v2Session['attireRequired'], $v1Session['attireRequired']);
        $this->assertEquals($v2Session['supplemental'], $v1Session['supplemental']);
        $this->assertEquals($v2Session['publishedAsTbd'], $v1Session['publishedAsTbd']);
        $this->assertEquals($v2Session['published'], $v1Session['published']);
        $this->assertEquals($v2Session['instructionalNotes'], $v1Session['instructionalNotes']);
        $this->assertEquals($v2Session['updatedAt'], $v1Session['updatedAt']);
        $this->assertEquals($v2Session['sessionType'], $v1Session['sessionType']);
        $this->assertEquals($v2Session['course'], $v1Session['course']);
        $this->assertEquals($v2Session['terms'], $v1Session['terms']);
        $this->assertEquals($v2Session['meshDescriptors'], $v1Session['meshDescriptors']);
        $this->assertEquals($v2Session['sessionDescription'], $v1Session['sessionDescription']);
        $this->assertEquals($v2Session['administrators'], $v1Session['administrators']);
        $this->assertEquals($v2Session['offerings'], $v1Session['offerings']);
        $this->assertEquals($v2Session['prerequisites'], $v1Session['prerequisites']);
        $this->assertEquals(count($v2Session['sessionObjectives']), count($v1Session['objectives']));
        $this->assertEquals($sessionObjective['objective'], $v1Session['objectives'][0]);
    }
}
