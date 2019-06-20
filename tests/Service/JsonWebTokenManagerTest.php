<?php
namespace App\Tests\Service;

use App\Classes\SessionUserInterface;
use App\Service\PermissionChecker;
use App\Service\SessionUserProvider;
use App\Tests\TestCase;
use Firebase\JWT\JWT;
use DateTime;
use Mockery as m;

use App\Service\JsonWebTokenManager;

class JsonWebTokenManagerTest extends TestCase
{
    /** @var JsonWebTokenManager */
    protected $obj;
    protected $permissionChecker;
    protected $sessionUserProvider;

    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->obj = new JsonWebTokenManager(
            $this->permissionChecker,
            $this->sessionUserProvider,
            'secret'
        );
    }

    /**
     * Remove all mock objects
     */
    public function tearDown() : void
    {
        unset($this->obj);
        unset($this->permissionChecker);
        unset($this->sessionUserProvider);
    }

    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof JsonWebTokenManager);
    }
    
    public function testGetUserIdFromToken()
    {
        $jwt = $this->buildToken();
        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
    }
    
    public function testGetIssuedAtFromToken()
    {
        $yesterday = new DateTime('yesterday');
        $stamp = $yesterday->format('U');
        $jwt = $this->buildToken(array('iat' => $stamp));
        $this->assertSame($stamp, $this->obj->getIssuedAtFromToken($jwt)->format('U'));
    }
    
    public function testCreateJwtFromSessionUser()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser);
        
        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
        $this->assertSame(true, $this->obj->getPerformsNonLearnerFunctionFromToken($jwt));
        $this->assertSame(true, $this->obj->getIsRootFromToken($jwt));
        $this->assertSame(true, $this->obj->getCanCreateOrUpdateUserInAnySchoolFromToken($jwt));
    }
    
    public function testCreateJwtFromSessionUserWhichExpiresNextWeek()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);
        
        $jwt = $this->obj->createJwtFromSessionUser($sessionUser, 'P1W');
        $now = new DateTime();
        $expiresAt = $this->obj->getExpiresAtFromToken($jwt);
        
        $this->assertTrue($now->diff($expiresAt)->d > 5);
    }
    
    public function testCreateJwtFromSessionUserWhichExpiresAfterMaximumTime()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);
        
        $jwt = $this->obj->createJwtFromSessionUser($sessionUser, 'P400D');
        $now = new DateTime();
        $expiresAt = $this->obj->getExpiresAtFromToken($jwt);

        $this->assertTrue($now->diff($expiresAt)->days < 365, 'maximum ttl not applied');
    }

    public function testCreateJwtFromSessionUserWithLessPrivileges()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $sessionUser->shouldReceive('isRoot')->once()->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(false);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(false);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser);

        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
        $this->assertSame(false, $this->obj->getIsRootFromToken($jwt));
        $this->assertSame(false, $this->obj->getPerformsNonLearnerFunctionFromToken($jwt));
        $this->assertSame(false, $this->obj->getCanCreateOrUpdateUserInAnySchoolFromToken($jwt));
    }
    
    protected function buildToken(array $values = array(), $secretKey = 'ilios.jwt.key.secret')
    {
        $now = new DateTime();
        $default = array(
            'iss' => 'ilios',
            'aud' => 'ilios',
            'iat' => $now->format('U'),
            'exp' => $now->modify('+1 year')->format('U'),
            'user_id' => 42
        );
        
        $merged = array_merge($default, $values);

        return JWT::encode($merged, $secretKey);
    }
}
