<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Entity\DTO\UserDTO;
use App\Entity\User;
use App\Service\Config;
use App\Service\Index\Users;
use App\Tests\TestCase;
use OpenSearch\Client;
use Exception;
use InvalidArgumentException;
use Mockery as m;

class UsersTest extends TestCase
{
    private m\MockInterface $client;
    private m\MockInterface $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->config);
    }

    public function testSetup(): void
    {
        $obj1 = new Users($this->config, $this->client);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Users($this->config, null);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexUsersThrowsWhenNotDTO(): void
    {
        $obj = new Users($this->config, null);
        $this->expectException(InvalidArgumentException::class);
        $users = [
            m::mock(UserDTO::class),
            m::mock(User::class),
            m::mock(UserDTO::class),
        ];
        $obj->index($users);
    }

    public function testIndexUsersWorksWithoutSearch(): void
    {
        $obj = new Users($this->config, null);
        $users = [
            $this->createUserDto(1),
            $this->createUserDto(2),
        ];
        $this->assertTrue($obj->index($users));
    }

    public function testIndexUsers(): void
    {
        $obj = new Users($this->config, $this->client);
        $user1 = $this->createUserDto(13);
        $user2 = $this->createUserDto(11);

        $this->client->shouldReceive('request')->withArgs(function ($method, $uri, $data) use ($user1, $user2) {
            $this->assertEquals('POST', $method);
            $this->assertEquals('/_bulk', $uri);
            $this->assertArrayHasKey('body', $data);
            $this->assertArrayHasKey('options', $data);
            $this->assertEquals(['headers' => ['Content-Encoding' => 'gzip']], $data['options']);
            $body = gzdecode($data['body']);
            $arr = array_map(fn ($item) => json_decode($item, true), explode("\n", $body));
            $filtered = array_filter($arr, 'is_array');
            $this->assertCount(4, $filtered);

            $this->assertEquals($user1->id, $filtered[1]['id']);
            $this->assertEquals($user1->firstName, $filtered[1]['firstName']);
            $this->assertEquals($user1->lastName, $filtered[1]['lastName']);
            $this->assertEquals($user1->middleName, $filtered[1]['middleName']);
            $this->assertEquals($user1->displayName, $filtered[1]['displayName']);
            $this->assertEquals($user1->email, $filtered[1]['email']);
            $this->assertEquals($user1->campusId, $filtered[1]['campusId']);
            $this->assertEquals($user1->username, $filtered[1]['username']);
            $this->assertEquals($user1->enabled, $filtered[1]['enabled']);
            $this->assertEquals('13 first 13 middle 13 last', $filtered[1]['fullName']);
            $this->assertEquals('13 last, 13 first 13 middle', $filtered[1]['fullNameLastFirst']);

            $this->assertEquals($user2->id, $filtered[3]['id']);
            $this->assertEquals($user2->firstName, $filtered[3]['firstName']);
            $this->assertEquals($user2->lastName, $filtered[3]['lastName']);
            $this->assertEquals($user2->middleName, $filtered[3]['middleName']);
            $this->assertEquals($user2->displayName, $filtered[3]['displayName']);
            $this->assertEquals($user2->email, $filtered[3]['email']);
            $this->assertEquals($user2->campusId, $filtered[3]['campusId']);
            $this->assertEquals($user2->username, $filtered[3]['username']);
            $this->assertEquals($user2->enabled, $filtered[3]['enabled']);
            $this->assertEquals('11 first 11 middle 11 last', $filtered[3]['fullName']);
            $this->assertEquals('11 last, 11 first 11 middle', $filtered[3]['fullNameLastFirst']);

            return true;
        })
        ->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$user1, $user2]);
    }

    public function testSearchThrowsExceptionWhenNotConfigured(): void
    {
        $obj = new Users($this->config, null);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->search('', 1, false);
    }

    protected function createUserDto(int $id): UserDTO
    {
        return new UserDTO(
            $id,
            "{$id} first",
            "{$id} last",
            "{$id} middle",
            null,
            null,
            "{$id}@{$id}}.com",
            null,
            null,
            true,
            true,
            null,
            null,
            true,
            false,
            "{$id}-ics",
            false
        );
    }
}
