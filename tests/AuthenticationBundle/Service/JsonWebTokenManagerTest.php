<?php
namespace Tests\AuthenticationBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Firebase\JWT\JWT;
use DateTime;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;

class JsonWebTokenManagerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testConstructor()
    {
        $obj = new JsonWebTokenManager('secretKey');
        $this->assertTrue($obj instanceof JsonWebTokenManager);
    }
    
    public function testGetUserIdFromToken()
    {
        $obj = new JsonWebTokenManager('secret');
        $jwt = $this->buildToken();
        $this->assertSame(42, $obj->getUserIdFromToken($jwt));
    }
    
    public function testGetIssuedAtFromToken()
    {
        $yesterday = new DateTime('yesterday');
        $stamp = $yesterday->format('U');
        $obj = new JsonWebTokenManager('secret');
        $jwt = $this->buildToken(array('iat' => $stamp));
        $this->assertSame($stamp, $obj->getIssuedAtFromToken($jwt)->format('U'));
    }
    
    public function testCreateJwtFromUser()
    {
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $obj = new JsonWebTokenManager('secret');
        
        $jwt = $obj->createJwtFromSessionUser($sessionUser);
        
        $this->assertSame(42, $obj->getUserIdFromToken($jwt));
    }
    
    public function testCreateJwtFromUserWhichExpiresNextWeek()
    {
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $obj = new JsonWebTokenManager('secret');
        
        $jwt = $obj->createJwtFromSessionUser($sessionUser, 'P1W');
        $now = new DateTime();
        $expiresAt = $obj->getExpiresAtFromToken($jwt);
        
        $this->assertTrue($now->diff($expiresAt)->d > 5);
    }
    
    public function testCreateJwtFromUserWhichExpiresAfterMaximumTime()
    {
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $obj = new JsonWebTokenManager('secret');
        
        $jwt = $obj->createJwtFromSessionUser($sessionUser, 'P400D');
        $now = new DateTime();
        $expiresAt = $obj->getExpiresAtFromToken($jwt);

        $this->assertTrue($now->diff($expiresAt)->days < 365, 'maximum ttl not applied');
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
