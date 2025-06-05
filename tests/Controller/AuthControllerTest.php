<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Controller\AuthController;
use App\Service\JsonWebTokenManager;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadServiceTokenData;
use App\Tests\GetUrlTrait;
use App\Tests\Traits\TestableJsonController;
use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function array_key_exists;
use function json_decode;
use function var_export;

#[Group('controller')]
#[CoversClass(AuthController::class)]
final class AuthControllerTest extends WebTestCase
{
    use TestableJsonController;
    use GetUrlTrait;

    protected string $apiVersion = 'v3';
    protected string $jwtKey;
    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class,
            LoadServiceTokenData::class,
        ]);
        $secret = $this->kernelBrowser->getContainer()->getParameter('kernel.secret');
        $this->jwtKey = JsonWebTokenManager::PREPEND_KEY . $secret;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testMissingValues(): void
    {
        $this->kernelBrowser->request('POST', '/auth/login');

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);

        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }

    public function testAuthenticateUser(): void
    {
        $this->kernelBrowser->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'newuser',
            'password' => 'newuserpass',
        ]));

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);

        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));

        $token = $this->decode($data->jwt);
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(2, $token['user_id']);
    }

    public function testAuthenticateUserCaseInsensitive(): void
    {
        $this->kernelBrowser->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'NEWUSER',
            'password' => 'newuserpass',
        ]));
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));

        $token = $this->decode($data->jwt);
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(2, $token['user_id']);
    }

    public function testWrongPassword(): void
    {
        $this->kernelBrowser->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'newuser',
            'password' => 'wrongnewuserpass',
        ]));

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);

        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testWhoAmI(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_whoami'),
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $response = json_decode($response->getContent(), true);

        $this->assertTrue(
            array_key_exists('userId', $response),
            'Response has user_id: ' . var_export($response, true)
        );
        $this->assertSame(
            $response['userId'],
            2,
            'Response has the correct user id: ' . var_export($response, true)
        );
    }

    public function testWhoAmIUnauthenticated(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_whoami'),
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function testWhoAmIExpiredToken(): void
    {
        $jwt = $this->getExpiredToken(1);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_whoami'),
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testGetToken(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $token = $this->decode($jwt);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_token'),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $response = json_decode($response->getContent(), true);
        $token2 = $this->decode($response['jwt']);

        // figure out the delta between issued and expiration datetime
        $exp = DateTime::createFromFormat('U', $token['exp']);
        $iat = DateTime::createFromFormat('U', $token['iat']);
        $interval = $iat->diff($exp);

        // do it again for the new token
        $exp2 = DateTime::createFromFormat('U', $token2['exp']);
        $iat2 = DateTime::createFromFormat('U', $token2['iat']);
        $interval2 = $iat2->diff($exp2);

        // test for sameness
        $this->assertSame($token['user_id'], $token2['user_id']);
        $this->assertSame($token['permissions'], $token2['permissions']);
        $this->assertSame($token['iss'], $token2['iss']);
        $this->assertSame($token['aud'], $token2['aud']);
        $this->assertSame($token['firstCreatedAt'], $token2['firstCreatedAt']);
        // http://php.net/manual/en/dateinterval.format.php
        $this->assertSame($interval->format('%R%Y/%M/%D %H:%I:%S'), $interval2->format('%R%Y/%M/%D %H:%I:%S'));

        //refresh should increment counter
        $this->assertEquals(0, $token['refreshCount']);
        $this->assertEquals(1, $token2['refreshCount']);

        //both tokens have user level permissions
        $this->assertEquals('user', $token['permissions']);
        $this->assertEquals('user', $token2['permissions']);
    }

    public function testGetTokenWithNonDefaultTtl(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_token') . '?ttl=P2W',
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();
        $response = json_decode($response->getContent(), true);
        $token = $this->decode($response['jwt']);


        $now = new DateTime();
        $expiresAt = DateTime::createFromFormat('U', $token['exp']);

        $this->assertTrue($now->diff($expiresAt)->d > 5);
    }

    public function testGetTokenForUnauthenticatedUser(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_token'),
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function testGetTokenForExpiredToken(): void
    {
        $jwt = $this->getExpiredToken(1);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_token'),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testInvalidateToken(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        sleep(1);

        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_invalidatetokens'),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_users_getone',
                ['version' => $this->apiVersion, 'id' => 1]
            ),
            null,
            $jwt
        );
        $response2 = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response2->getStatusCode());
        $this->assertMatchesRegularExpression('/Invalid JSON Web Token: Not issued after/', $response2->getContent());
    }

    public function testInvalidateTokenForUnauthenticatedUser(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_invalidatetokens'),
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function testGetTokenDeniedForServiceToken(): void
    {
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_token'),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testInvalidateTokenDeniedForServiceToken(): void
    {
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_invalidatetokens'),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testWhoAmiIDeniedForServiceToken(): void
    {
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'get',
            $this->getUrl($this->kernelBrowser, 'app_auth_whoami'),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    protected function getExpiredToken(int $userId): string
    {
        $container = $this->kernelBrowser->getContainer();
        /** @var JsonWebTokenManager $jwtManager */
        $jwtManager = $container->get(JsonWebTokenManager::class);
        $jwt = $jwtManager->createJwtFromUserId($userId, 'PT0S');
        sleep(6); //wait for 5 second leeway to pass
        return $jwt;
    }

    protected function decode(string $jwt): array
    {
        $decoded = JWT::decode($jwt, new Key($this->jwtKey, JsonWebTokenManager::SIGNING_ALGORITHM));
        return (array) $decoded;
    }
}
