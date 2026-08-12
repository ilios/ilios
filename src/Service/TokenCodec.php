<?php

declare(strict_types=1);

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

/**
 * JWT encoder/decoder.
 */
class TokenCodec
{
    protected string $jwtKey;

    public const string PREPEND_KEY = 'ilios.jwt.key.';

    public const string SIGNING_ALGORITHM = 'HS256';

    public function __construct(
        protected readonly SecretManager $secretManager,
    ) {
        $this->jwtKey = self::PREPEND_KEY . $this->secretManager->getSecret();
    }

    public function decode(string $jwt): array
    {
        JWT::$leeway = 5;
        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtKey, self::SIGNING_ALGORITHM));
            return (array)$decoded;
        } catch (SignatureInvalidException $e) {
            $transitionalSecret = $this->secretManager->getTransitionalSecret();
            if ($transitionalSecret) {
                $transitionalKey = self::PREPEND_KEY . $transitionalSecret;
                $decoded = JWT::decode($jwt, new Key($transitionalKey, self::SIGNING_ALGORITHM));
                return (array)$decoded;
            }
            throw $e;
        }
    }

    public function encode(array $data): string
    {
        return JWT::encode($data, $this->jwtKey, self::SIGNING_ALGORITHM);
    }
}
