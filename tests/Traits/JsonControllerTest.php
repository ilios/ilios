<?php
namespace App\Tests\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Service\JsonWebTokenManager;

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
     * @param KernelBrowser $browser
     * @return string the JWT
     */
    protected function getAuthenticatedUserToken(KernelBrowser $browser)
    {
        return $this->getTokenForUser($browser, 2);
    }


    /**
     * Logs in a specific user and returns the token for them
     *
     * @param KernelBrowser $browser
     * @param string $userId
     * @return string the JWT
     */
    protected function getTokenForUser(KernelBrowser $browser, $userId)
    {
        static $tokens;

        if (!is_array($tokens)) {
            $tokens = [];
        }
        $userId = (int) $userId;

        if (!array_key_exists($userId, $tokens)) {
            /** @var ContainerInterface $container **/
            $container = $browser->getContainer();

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
     * @param KernelBrowser $client
     * @param string $method
     * @param string $url
     * @param string $content
     * @param string $token
     * @param array $files
     */
    public function makeJsonRequest(
        KernelBrowser $client,
        $method,
        $url,
        $content = null,
        $token = null,
        $files = array()
    ) {
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
     * @param KernelBrowser $browser
     * @param string $userId
     * @param string $method
     * @param string $url
     * @param string $data
     */
    protected function canNot(KernelBrowser $browser, $userId, $method, $url, $data = null)
    {
        $this->makeJsonRequest(
            $browser,
            $method,
            $url,
            $data,
            $this->getTokenForUser($browser, $userId)
        );

        $response = $browser->getResponse();
        $this->assertEquals(
            Response::HTTP_FORBIDDEN,
            $response->getStatusCode(),
            "User #{$userId} should be prevented from {$method} at {$url}" .
            substr(var_export($response->getContent(), true), 0, 400)
        );
    }
}
