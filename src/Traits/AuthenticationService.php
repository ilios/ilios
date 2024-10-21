<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait AuthenticationService
{
    protected function createSuccessResponseFromJWT(string $jwt): JsonResponse
    {
        $response =  new JsonResponse([
            'status' => 'success',
            'errors' => [],
            'jwt' => $jwt,
        ], Response::HTTP_OK);
        $response->headers->set('X-JWT-TOKEN', $jwt);

        return $response;
    }
}
