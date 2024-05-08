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
    protected UserSessionMaterialStatus $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new UserSessionMaterialStatus();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'user',
            'material',
            'status',
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
    public function testConstructor(): void
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
    public function testSetStatus(): void
    {
        $this->object->setStatus(UserSessionMaterialStatusInterface::NONE);
        $this->assertSame(UserSessionMaterialStatusInterface::NONE, $this->object->getStatus());
    }

    /**
     * @covers \App\Entity\UserSessionMaterialStatus::setUser
     * @covers \App\Entity\UserSessionMaterialStatus::getUser
     */
    public function testSetUser(): void
    {
        $this->entitySetTest('user', "User");
    }

    /**
     * @covers \App\Entity\UserSessionMaterialStatus::setMaterial
     * @covers \App\Entity\UserSessionMaterialStatus::getMaterial
     */
    public function testSetMaterial(): void
    {
        $this->entitySetTest('material', "SessionLearningMaterial");
    }

    public function getObject(): UserSessionMaterialStatus
    {
        return $this->object;
    }
}
