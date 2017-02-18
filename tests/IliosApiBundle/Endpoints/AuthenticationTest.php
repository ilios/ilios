<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Authentication API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AuthenticationTest extends AbstractEndpointTest
{
    protected $testName =  'authentications';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'username' => ['username', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'user' => ['user', 1, 99],
            'invalidateTokenIssuedBefore' => ['invalidateTokenIssuedBefore', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'user' => [[0], ['user' => 'test']],
            'username' => [[0], ['username' => 'test']],
            'invalidateTokenIssuedBefore' => [[0], ['invalidateTokenIssuedBefore' => 'test']],
        ];
    }

}