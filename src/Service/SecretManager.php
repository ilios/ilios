<?php

declare(strict_types=1);

namespace App\Service;

class SecretManager
{
    public function __construct(
        protected readonly string $kernelSecret,
        protected readonly ?string $transitionalSecret,
    ) {
    }

    public function getSecret(): string
    {
        return $this->kernelSecret;
    }

    public function getTransitionalSecret(): ?string
    {
        return $this->transitionalSecret;
    }
}
