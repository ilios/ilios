<?php

namespace Ilios\AuthenticationBundle\Classes;

/**
 * Class PermissionMatrix
 * @package Ilios\AuthenticationBundle\Classes
 */
class PermissionMatrix implements PermissionMatrixInterface
{
    /**
     * @var array
     */
    protected $matrix = [];

    /**
     * @inheritdoc
     */
    public function hasPermission(int $schoolId, string $capability, array $roles): bool
    {
        if (!array_key_exists($schoolId, $this->matrix)) {
            return false;
        }
        $schoolPermissions = $this->matrix[$schoolId];
        if (!array_key_exists($capability, $schoolPermissions)) {
            return false;
        };

        $permittedRoles = $schoolPermissions[$capability];

        $hasPermission = false;
        while (!$hasPermission && !empty($roles)) {
            $role = array_pop($roles);
            $hasPermission = in_array($role, $permittedRoles);
        }

        return $hasPermission;
    }

    /**
     * @inheritdoc
     */
    public function setPermission(int $schoolId, string $capability, array $roles)
    {
        if (!array_key_exists($schoolId, $this->matrix)) {
            $this->matrix[$schoolId] = [];
        }
        $this->matrix[$schoolId][$capability] = $roles;
    }
}
