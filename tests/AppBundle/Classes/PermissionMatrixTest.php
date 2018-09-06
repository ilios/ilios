<?php

namespace Tests\AppBundle\Classes;

use AppBundle\Classes\PermissionMatrix;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class PermissionMatrixTest
 * @package Tests\AppBundle\Classes
 */
class PermissionMatrixTest extends TestCase
{
    /**
     * @var PermissionMatrix
     */
    protected $permissionMatrix;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->permissionMatrix = new PermissionMatrix();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->permissionMatrix);
    }

    /**
     * @covers PermissionMatrix::setPermission
     * @covers PermissionMatrix::hasPermission
     * @covers PermissionMatrix::getPermittedRoles
     */
    public function testHasPermission()
    {
        $schoolId = 1;
        $capability = 'foo';
        $role1 = 'lorem';
        $role2 = 'ipsum';
        $role3 = 'dolor';

        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1]));
        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role2]));
        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role3]));


        $this->permissionMatrix->setPermission($schoolId, $capability, [$role1, $role2]);

        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role2]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1, $role2]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1, $role2, $role3]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1, $role3]));
        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role3]));
    }

    /**
     * @covers PermissionMatrix::getPermittedRoles
     */
    public function testGetPermittedRoles()
    {
        $schoolId = 1;
        $capability = 'foo';
        $role1 = 'lorem';
        $role2 = 'ipsum';

        $this->assertEmpty($this->permissionMatrix->getPermittedRoles($schoolId, $capability));
        $this->permissionMatrix->setPermission($schoolId, $capability, [$role1, $role2]);
        $permittedRoles = $this->permissionMatrix->getPermittedRoles($schoolId, $capability);
        $this->assertEquals(2, count($permittedRoles));
        $this->assertTrue(in_array($role1, $permittedRoles));
        $this->assertTrue(in_array($role2, $permittedRoles));
    }
}
