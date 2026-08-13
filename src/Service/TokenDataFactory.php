<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\TokenData;

/**
 * A factory that creates token data objects.
 */
readonly class TokenDataFactory
{
    public function __construct(protected TokenCodec $codec)
    {
    }

    public function createFromJwt(string $jwt): TokenData
    {
        $data = $this->codec->decode($jwt);
        return $this->createFromArray($data);
    }

    public function createFromArray(array $data): TokenData
    {
        return new TokenData($data);
    }
}
