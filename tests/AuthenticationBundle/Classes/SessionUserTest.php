<?php

namespace Tests\AuthenticationBundle\Classes;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class SessionUserTest
 * @package Tests\AuthenticationBundle\Classes
 */
class SessionUserTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $iliosUser;

    protected $userManager;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->iliosUser = m::mock(UserInterface::class);

        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);

        $this->iliosUser->shouldReceive('getId')->andReturn(1);
        $this->iliosUser->shouldReceive('isRoot')->andReturn(false);
        $this->iliosUser->shouldReceive('isEnabled')->andReturn(true);
        $this->iliosUser->shouldReceive('getSchool')->andReturn($school);
        $this->iliosUser->shouldReceive('getAuthentication')->andReturn(null);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->iliosUser);
        unset($this->userManager);
        unset($this->config);
    }
}
