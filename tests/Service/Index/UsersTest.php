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

        $this->client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => Users::INDEX,
                        '_id' => $user1->id,
                    ],
                ],
                [
                    'id' => $user1->id,
                    'firstName' => $user1->firstName,
                    'lastName' => $user1->lastName,
                    'middleName' => $user1->middleName,
                    'displayName' => $user1->displayName,
                    'email' => $user1->email,
                    'campusId' => $user1->campusId,
                    'username' => $user1->username,
                    'enabled' => $user1->enabled,
                    'fullName' => '13 first 13 middle 13 last',
                    'fullNameLastFirst' => '13 last, 13 first 13 middle',
                ],
                [
                    'index' => [
                        '_index' => Users::INDEX,
                        '_id' => $user2->id,
                    ],
                ],
                [
                    'id' => $user2->id,
                    'firstName' => $user2->firstName,
                    'lastName' => $user2->lastName,
                    'middleName' => $user2->middleName,
                    'displayName' => $user2->displayName,
                    'email' => $user2->email,
                    'campusId' => $user2->campusId,
                    'username' => $user2->username,
                    'enabled' => $user2->enabled,
                    'fullName' => '11 first 11 middle 11 last',
                    'fullNameLastFirst' => '11 last, 11 first 11 middle',
                ],
            ],
        ])->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
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
