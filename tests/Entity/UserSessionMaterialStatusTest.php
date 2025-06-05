<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\UserInterface;
use App\Entity\UserSessionMaterialStatus;
use App\Entity\UserSessionMaterialStatusInterface;
use DateTime;
use Mockery as m;

#[Group('model')]
#[CoversClass(UserSessionMaterialStatus::class)]
final class UserSessionMaterialStatusTest extends EntityBase
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
    public function testConstructor(): void
    {
        $now = new DateTime();
        $this->assertInstanceOf(DateTime::class, $this->object->getUpdatedAt());
        $diff = $now->diff($this->object->getUpdatedAt());
        $this->assertTrue($diff->s < 2);
    }

    public function testSetStatus(): void
    {
        $this->object->setStatus(UserSessionMaterialStatusInterface::NONE);
        $this->assertSame(UserSessionMaterialStatusInterface::NONE, $this->object->getStatus());
    }

    public function testSetUser(): void
    {
        $this->entitySetTest('user', "User");
    }

    public function testSetMaterial(): void
    {
        $this->entitySetTest('material', "SessionLearningMaterial");
    }

    public function getObject(): UserSessionMaterialStatus
    {
        return $this->object;
    }
}
