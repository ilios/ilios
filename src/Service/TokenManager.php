<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ServiceToken;
use App\Classes\UserToken;

/**
 * Utilities for dealing with authentication tokens.
 */
readonly class TokenManager
{
    public function __construct(protected TokenCodec $codec, protected TokenFactory $factory)
    {
    }

    /**
     * Extracts and returns data from a given JSON Web Token (JWT).
     * @param string $jwt The encoded token.
     * @return UserToken|ServiceToken The object representation of the given tokne.
     */
    public function extractJwt(string $jwt): UserToken|ServiceToken
    {
        $data = $this->codec->decode($jwt);
        return $this->factory->create($data);
    }
}
