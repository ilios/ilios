<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SessionLearningMaterialInterface;
use App\Entity\UserInterface;
use App\Entity\UserSessionMaterialStatus;
use App\Entity\UserSessionMaterialStatusInterface;
use DateTime;
use Mockery as m;

/**
 * @group model
 */
class UserSessionMaterialStatusTest extends EntityBase
{
    /**
     * @var UserSessionMaterialStatus
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new UserSessionMaterialStatus();
    }

    public function testNotNullValidation()
    {
        $notNull = [
            'user',
            'material',
            'status'
        ];
        $this->validateNotNulls($notNull);

        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setMaterial(m::mock(SessionLearningMaterialInterface::class));
        $this->object->setStatus(UserSessionMaterialStatusInterface::COMPLETE);

        $this->validate(0);
    }
    /**
     * @covers \App\Entity\UserSessionMaterialStatus::__construct
     */
    public function testConstructor()
    {
        $now = new DateTime();
        $this->assertInstanceOf(DateTime::class, $this->object->getUpdatedAt());
        $diff = $now->diff($this->object->getUpdatedAt());
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\UserSessionMaterialStatus::setStatus
     * @covers \App\Entity\UserSessionMaterialStatus::getStatus
     */
    public function testSetStatus()
    {
        $this->object->setStatus(UserSessionMaterialStatusInterface::NONE);
        $this->assertSame(UserSessionMaterialStatusInterface::NONE, $this->object->getStatus());
    }

    /**
     * @covers \App\Entity\UserSessionMaterialStatus::setUser
     * @covers \App\Entity\UserSessionMaterialStatus::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', "User");
    }

    /**
     * @covers \App\Entity\UserSessionMaterialStatus::setMaterial
     * @covers \App\Entity\UserSessionMaterialStatus::getMaterial
     */
    public function testSetMaterial()
    {
        $this->entitySetTest('material', "SessionLearningMaterial");
    }
}
