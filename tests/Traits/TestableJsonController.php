<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Classes\Jwt\ServiceToken;
use App\Entity\School;
use App\Service\Jwt\TokenCodec;
use App\Service\Jwt\TokenManager;
use App\Service\SessionUserProvider;
use App\Tests\DataLoader\ServiceTokenData;
use App\Tests\DataLoader\UserData;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function substr;

trait TestableJsonController
{
    /**
     * Create a JSON:API request
     */
    public function makeJsonApiRequest(
        KernelBrowser $client,
        string $method,
        string $url,
        ?string $content,
        ?string $jwt,
        array $files = []
    ): void {
        $headers = [
            'HTTP_ACCEPT' => 'application/vnd.api+json',
        ];

        if (! empty($jwt)) {
            $headers['HTTP_X-JWT-Authorization'] = 'Token ' . $jwt;
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
     * Create a JSON request
     *
     */
    public function makeJsonRequest(
        KernelBrowser $client,
        string $method,
        string $url,
        ?string $content = null,
        ?string $jwt = null,
        array $files = []
    ): void {
        $headers = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        if (! empty($jwt)) {
            $headers['HTTP_X-JWT-Authorization'] = 'Token ' . $jwt;
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
     * Check if the response is valid
     * tests the status code, headers, and the content
     */
    protected function assertJsonResponse(Response $response, int $statusCode, bool $checkValidJson = true): void
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

            $this->assertNotNull($decode, 'Invalid JSON: [' . $response->getContent() . ']');
        }
    }

    /**
     * Check if the response is valid
     * tests the status code, headers, and the content
     */
    protected function assertJsonApiResponse(Response $response, int $statusCode, bool $checkValidJson = true): void
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
                    'application/vnd.api+json'
                ),
                "Content-type is not application/vnd.api+json. \n" .
                "Headers: [\n" . $response->headers . ']' .
                "Content: [\n" . substr($response->getContent(), 0, 1000) . ']'
            );

            $decode = json_decode($response->getContent());

            $this->assertNotNull($decode, 'Invalid JSON: [' . $response->getContent() . ']');
            $this->assertIsObject($decode);
            $this->assertTrue(property_exists($decode, 'data'));
            $this->assertTrue(property_exists($decode, 'included'));
        }
    }


    /**
     * Check if the response is valid graphQL
     * tests the status code, headers, and the content
     */
    protected function assertGraphQLResponse(Response $response): void
    {
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            'Wrong Response Status.  Page Body: ' . substr($response->getContent(), 0, 1000)
        );

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
            ($decode !== null && $decode !== false),
            'Invalid JSON: [' . $response->getContent() . ']'
        );
        $this->assertIsObject($decode);
        $this->assertTrue(
            property_exists($decode, 'data'),
            var_export($decode->errors ?? 'Unknown Error', true)
        );
    }

    /**
     * Tests to ensure that a user cannot access a certain function
     */
    protected function canNot(
        KernelBrowser $browser,
        string $jwt,
        string $method,
        string $url,
        ?string $data = null
    ): void {
        $this->makeJsonRequest(
            $browser,
            $method,
            $url,
            $data,
            $jwt
        );

        $response = $browser->getResponse();
        $this->assertEquals(
            Response::HTTP_FORBIDDEN,
            $response->getStatusCode(),
            "Access denied from $method at $url" .
            substr(var_export($response->getContent(), true), 0, 400)
        );
    }

    /**
     * Tests to ensure that a user cannot access a certain function
     */
    protected function canNotJsonApi(
        KernelBrowser $browser,
        string $jwt,
        string $method,
        string $url,
        ?string $data = null
    ): void {
        $this->makeJsonApiRequest(
            $browser,
            $method,
            $url,
            $data,
            $jwt,
        );
        $response = $browser->getResponse();
        $this->assertEquals(
            Response::HTTP_FORBIDDEN,
            $response->getStatusCode(),
            "Access denied from $method at $url" .
            substr(var_export($response->getContent(), true), 0, 400)
        );
    }


    protected function createJwtForRootUser(KernelBrowser $browser): string
    {
        return $this->createJwtFromUserId($browser, UserData::ROOT_USER_ID);
    }

    /**
     * Creates a user-based JWT for a given user id.
     *
     * @param int $userId the user ID
     * @return string the generated JWT
     */
    protected function createJwtFromUserId(KernelBrowser $browser, int $userId): string
    {
        $container = $browser->getContainer();
        $tokenCodec = $container->get(TokenCodec::class);
        $tokenManager = $container->get(TokenManager::class);
        $sessionUserProvider = $container->get(SessionUserProvider::class);
        $sessionUser = $sessionUserProvider->createSessionUserFromUserId($userId);
        $token = $tokenManager->createUserTokenForSessionUser($sessionUser);
        return $tokenCodec->encode($token);
    }

    /**
     * Creates a service-token based JWT for an active, un-expired service token,
     * optionally with write permissions to given schools.
     *
     * @param array $writeableSchoolIds The IDs of schools that this token has write-permissions to.
     * @param bool $canCreateUserTokens TRUE if the service token can be used to create user tokens.
     * @param bool $grantLtiDashboardAudienceClaim TRUE if the service token is granted the LTI-dashboard audience claim
     * @return string the generated JWT
     */
    protected function createJwtForEnabledServiceToken(
        KernelBrowser $browser,
        array $writeableSchoolIds = [],
        bool $canCreateUserTokens = false,
        bool $grantLtiDashboardAudienceClaim = false,
    ): string {
        return $this->createJwtFromServiceTokenId(
            $browser,
            ServiceTokenData::ENABLED_SERVICE_TOKEN_ID,
            $writeableSchoolIds,
            $canCreateUserTokens,
            $grantLtiDashboardAudienceClaim
        );
    }

    /**
     * Creates a service-token based JWT for a given service token, optionally with write permissions to given schools.
     *
     * @param int $serviceTokenId the service token id
     * @param array $writeableSchoolIds The IDs of schools that this token has write-permissions to.
     * @param bool $canCreateUserTokens TRUE if the service token can be used to create user tokens.
     * @param bool $grantLtiDashboardAudienceClaim TRUE if the service token is granted the LTI-dashboard audience claim
     * @return string the generated JWT
     */
    protected function createJwtFromServiceTokenId(
        KernelBrowser $browser,
        int $serviceTokenId,
        array $writeableSchoolIds = [],
        bool $canCreateUserTokens = false,
        bool $grantLtiDashboardAudienceClaim = false,
    ): string {
        $container = $browser->getContainer();
        $tokenCodec = $container->get(TokenCodec::class);
        $iat = new DateTimeImmutable();
        $exp = $iat->add(new DateInterval(TokenManager::SERVICE_TOKEN_MAX_TTL));
        $aud = $grantLtiDashboardAudienceClaim
            ? TokenManager::TOKEN_LTI_DASHBOARD_AUDIENCE
            : TokenManager::TOKEN_DEFAULT_AUDIENCE;
        $serviceToken = new ServiceToken(
            $iat,
            $exp,
            [$aud],
            TokenManager::TOKEN_DEFAULT_ISSUER,
            $serviceTokenId,
            $writeableSchoolIds,
            $canCreateUserTokens
        );
        return $tokenCodec->encode($serviceToken);
    }

    /**
     * Utility method for creating a service-token-based JWT that's enabled and has write access to
     * all schools defined in our test vectors.
     *
     * @return string the JWT
     */
    protected function createJwtFromServiceTokenWithWriteAccessInAllSchools(
        KernelBrowser $kernelBrowser,
        ReferenceRepository $fixtures
    ): string {
        $schools = $fixtures->getReferencesByClass()[School::class];
        $schoolIds = array_values(array_map(fn(School $school) => $school->getId(), $schools));
        return $this->createJwtForEnabledServiceToken($kernelBrowser, $schoolIds);
    }
}
