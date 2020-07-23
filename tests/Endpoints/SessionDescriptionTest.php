<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\AbstractEndpointTest;

/**
 * SessionDescription API endpoint Test.
 * @group api_5
 * @todo Re-implement. [ST 2020/07/23]
 * @mark
 */
class SessionDescriptionTest extends AbstractEndpointTest
{
    protected $testName =  'sessionDescriptions';

    protected $apiVersion = 'v1';

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('to be implemented');
    }

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSessionData'
        ];
    }
}
