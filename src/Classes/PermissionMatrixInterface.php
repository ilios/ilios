<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Interface PermissionMatrixInterface
 * @package App\Classes
 */
interface PermissionMatrixInterface
{
    public function hasPermission(int $schoolId, string $capability, array $roles): bool;

    public function setPermission(int $schoolId, string $capability, array $roles): void;

    /**
     * Returns a list of roles that have the given capability in a given school.
     */
    public function getPermittedRoles(int $schoolId, string $capability): array;
}
