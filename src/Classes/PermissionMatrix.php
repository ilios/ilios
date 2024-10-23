<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Class PermissionMatrix
 * @package App\Classes
 */
class PermissionMatrix implements PermissionMatrixInterface
{
    protected array $matrix = [];

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

    public function setPermission(int $schoolId, string $capability, array $roles): void
    {
        if (!array_key_exists($schoolId, $this->matrix)) {
            $this->matrix[$schoolId] = [];
        }
        $this->matrix[$schoolId][$capability] = $roles;
    }

    public function getPermittedRoles(int $schoolId, string $capability): array
    {
        if (!array_key_exists($schoolId, $this->matrix)) {
            return [];
        }
        $schoolPermissions = $this->matrix[$schoolId];
        if (!array_key_exists($capability, $schoolPermissions)) {
            return [];
        };

        return $schoolPermissions[$capability];
    }
}
