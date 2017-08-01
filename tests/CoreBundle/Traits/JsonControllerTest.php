<?php
namespace Tests\CoreBundle\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;

/**
 * Class JsonControllerTest
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
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 1000)
        );

        if ($checkValidJson) {
            $this->assertTrue(
                $response->headers->contains(
                    'Content-Type',
                    'application/json'
                ),
                "Content-type is not application/json. \n" .
                "Headers: [\n" . $response->headers . ']' .
                "Content: [\n" . substr($response->getContent(), 0, 1000) . ']'
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
     */
    protected function getAuthenticatedUserToken()
    {
        return $this->getTokenForUser(2);
    }


    /**
     * Logs in a specific user and returns the token for them
     *
     * @param string $userId
     * @return string the JWT
     */
    protected function getTokenForUser($userId)
    {
        static $tokens;

        if (!is_array($tokens)) {
            $tokens = [];
        }
        $userId = (int) $userId;

        if (!array_key_exists($userId, $tokens)) {
            $client = $this->createClient();

            /** @var ContainerInterface $container **/
            $container = $client->getContainer();

            /** @var JsonWebTokenManager $jwtManager **/
            $jwtManager = $container->get(JsonWebTokenManager::class);
            $token = $jwtManager->createJwtFromUserId($userId);

            $tokens[$userId] = $token;
        }

        return $tokens[$userId];
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

    /**
     * Tests to ensure that a user cannot access a certain function
     *
     * @param string $userId
     * @param string $method
     * @param string $url
     * @param string $data
     */
    protected function canNot($userId, $method, $url, $data = null)
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            $method,
            $url,
            $data,
            $this->getTokenForUser($userId)
        );

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_FORBIDDEN,
            $response->getStatusCode(),
            "User #{$userId} should be prevented from {$method} at {$url}" .
            substr(var_export($response->getContent(), true), 0, 400)
        );
    }
}
