<?php
namespace Tests\CoreBundle\Traits;

use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class JsonControllerTest
 * @package Tests\CoreBundle\\Traits
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
    

    /**
     * Logs the 'newuser' user in and returns the user's JSON Web Token (JWT).
     * @return string the JWT
     * @todo obviously, this needs expanded in order to allow other user log-ins. [ST 2015/08/06]
     */
    protected function getAuthenticatedUserToken()
    {
        static $token;

        if (! $token) {
            $client = $this->createClient();
            $client->request(
                'POST',
                '/auth/login',
                array(
                    'username' => 'newuser',
                    'password' => 'newuserpass',
                )
            );
            $response = $client->getResponse();
            $this->assertJsonResponse($response, Codes::HTTP_OK);
            $response = json_decode($response->getContent(), true);
            $token = $response['jwt'];
        }

        return $token;
    }

    /**
     * Create a JSON request
     *
     * @param Client $client
     * @param string $method
     * @param string $url
     * @param string $content
     * @param string $token
     */
    public function makeJsonRequest(Client $client, $method, $url, $content = null, $token = null, $files = array())
    {
        $headers = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json'
        ];

        if (! empty($token)) {
            $headers['HTTP_X-JWT-Authorization'] = 'Token ' . $token;
        }

        $client->request(
            $method,
            $url,
            [],
            $files,
            $headers,
            $content
        );
    }
}
