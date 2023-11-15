<?php

declare(strict_types=1);

namespace App\Traits;

use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait ApiAccessValidation
{
    protected TokenStorageInterface $tokenStorage;

    /**
     * Checks if the current session user is user-based.
     * If not, then an exception is raised.
     *
     * Use this method in API endpoints to block out authorized requests with service-token based JWTs.
     *
     * @throws AccessDeniedException
     */
    protected function validateCurrentUserAsSessionUser(): void
    {
        $currentUser = $this->tokenStorage->getToken()?->getUser();
        if (! $currentUser instanceof SessionUserInterface) {
            throw new AccessDeniedException('Unauthorized access.');
        }
    }
}
