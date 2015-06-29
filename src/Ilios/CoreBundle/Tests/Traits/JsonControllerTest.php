<?php
namespace Ilios\CoreBundle\Tests\Traits;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonControllerTest
 * @package Ilios\CoreBundle\Tests\Traits
 */
trait JsonControllerTest
{
    /**
     * Check if the response is valid
     * tests the status code, headers, and the content
     * @param Response $response
     * @param integer $statusCode
     * @param boolean $checkValidJson
     */
    protected function assertJsonResponse(Response $response, $statusCode, $checkValidJson = true)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent());

        if ($checkValidJson) {
            $this->assertTrue(
                $response->headers->contains(
                    'Content-Type',
                    'application/json'
                ),
                $response->headers
            );

            $decode = json_decode($response->getContent());

            $this->assertTrue(
                ($decode != null && $decode != false),
                'Invalid JSON: [' . $response->getContent() . ']'
            );
        }
    }
}
