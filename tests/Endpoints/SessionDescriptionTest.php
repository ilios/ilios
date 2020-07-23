<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\AbstractEndpointTest;
use App\Tests\DataLoader\SessionData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use DateTime;

/**
 * SessionDescription API endpoint Test.
 * @group api_5
 */
class SessionDescriptionTest extends AbstractEndpointTest
{
    protected $testName =  'sessionDescriptions';

    protected $apiVersion = 'v1';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            LoadSessionData::class,
            LoadSessionObjectiveData::class,
            LoadSessionLearningMaterialData::class,
            LoadOfferingData::class,
        ];
    }

    public function testGetOne()
    {
        $sessionDataLoader = $this->getContainer()->get(SessionData::class);
        $sessionData = $sessionDataLoader->getOne();
        $session = $this->getOne('sessions', 'sessions', $sessionData['id']);
        $sessionV3 = $this->getOne('sessions', 'sessions', $sessionData['id'], 'v3');
        $sessionDescription = $this->getOne(
            'sessiondescriptions',
            'sessionDescriptions',
            $session['sessionDescription']
        );
        $this->assertNotEmpty($sessionDescription['description']);
        $this->assertEquals($sessionV3['description'], $sessionDescription['description']);
    }

    public function testCreateDescription()
    {
        $sessionDataLoader = $this->getContainer()->get(SessionData::class);
        $sessionData = $sessionDataLoader->create();
        $description = 'Lorem Ipsum';
        $sessionData['description'] = $description;
        $sessionV3 = $this->postOne('sessions', 'session', 'sessions', $sessionData, 'v3');
        $session = $this->getOne('sessions', 'sessions', $sessionV3['id']);
        $sessionDescription = $this->getOne(
            'sessiondescriptions',
            'sessionDescriptions',
            $session['sessionDescription']
        );
        $this->assertEquals($description, $sessionDescription['description']);
    }

    public function testUpdateDescription()
    {
        $sessionDataLoader = $this->getContainer()->get(SessionData::class);
        $sessionData = $sessionDataLoader->getOne();
        $oldDescription = $sessionData['description'];
        $newDescription = (new DateTime())->format('Y-m-d H:i:s');
        $sessionData['description'] = $newDescription;
        $this->putOne(
            'sessions',
            'session',
            $sessionData['id'],
            $sessionData,
            false,
            2,
            'v3'
        );
        $session = $this->getOne('sessions', 'sessions', $sessionData['id']);
        $sessionDescription = $this->getOne(
            'sessiondescriptions',
            'sessionDescriptions',
            $session['sessionDescription']
        );
        $this->assertNotEquals($oldDescription, $newDescription);
        $this->assertEquals($newDescription, $sessionDescription['description']);
    }
}
