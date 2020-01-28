<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Entity\DTO\UserDTO;
use App\Entity\User;
use App\Service\Config;
use App\Service\Index\Users;
use App\Tests\TestCase;
use Elasticsearch\Client;
use Mockery as m;

class UsersTest extends TestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $client;

    /**
     * @var Config|m\MockInterface
     */
    private $config;

    public function setup(): void
    {
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('elasticsearch_upload_limit')
            ->andReturn(8000000);
    }
    public function tearDown(): void
    {
        unset($this->client);
        unset($this->config);
    }

    public function testSetup()
    {
        $obj1 = new Users($this->config, $this->client);
        self::assertTrue($obj1 instanceof Users);
        self::assertTrue($obj1->isEnabled());

        $obj2 = new Users($this->config, null);
        self::assertTrue($obj2 instanceof Users);
        self::assertFalse($obj2->isEnabled());
    }

    public function testIndexUsersThrowsWhenNotDTO()
    {
        $obj = new Users($this->config, null);
        $this->expectException(\InvalidArgumentException::class);
        $users = [
            m::mock(UserDTO::class),
            m::mock(User::class),
            m::mock(UserDTO::class)
        ];
        $obj->index($users);
    }

    public function testIndexUsersWorksWithoutSearch()
    {
        $obj = new Users($this->config, null);
        $users = [
            m::mock(UserDTO::class),
            m::mock(UserDTO::class)
        ];
        self::assertTrue($obj->index($users));
    }


    public function testIndexUsers()
    {
        $obj = new Users($this->config, $this->client);
        $user1 = m::mock(UserDTO::class);
        $user1->id = 13;
        $user1->firstName = 'first';
        $user1->middleName = 'middle';
        $user1->lastName = 'last';
        $user1->displayName = 'display name';
        $user1->email = 'jackson@awesome.com';
        $user1->enabled = false;
        $user1->campusId = '99';
        $user1->username = 'thebestone';

        $user2 = m::mock(UserDTO::class);
        $user2->id = 11;
        $user2->firstName = 'first2';
        $user2->middleName = 'middle2';
        $user2->lastName = 'last2';
        $user2->displayName = null;
        $user2->email = 'jasper@awesome.com';
        $user1->enabled = true;
        $user2->campusId = 'OG';
        $user2->username = null;

        $this->client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => Users::INDEX,
                        '_type' => '_doc',
                        '_id' => $user1->id
                    ]
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
                    'fullName' => 'first middle last',
                    'fullNameLastFirst' => 'last, first middle',
                ],
                [
                    'index' => [
                        '_index' => Users::INDEX,
                        '_type' => '_doc',
                        '_id' => $user2->id
                    ]
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
                    'fullName' => 'first2 middle2 last2',
                    'fullNameLastFirst' => 'last2, first2 middle2',
                ],
            ]
        ])->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$user1, $user2]);
    }

    public function testSearchThrowsExceptionWhenNotConfigured()
    {
        $obj = new Users($this->config, null);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->search('', 1, false);
    }
}
