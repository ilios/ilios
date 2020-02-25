<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait AuthenticationService
{
    protected function createSuccessResponseFromJWT($jwt)
    {
        $response =  new JsonResponse([
            'status' => 'success',
            'errors' => [],
            'jwt' => $jwt,
        ], JsonResponse::HTTP_OK);
        $response->headers->set('X-JWT-TOKEN', $jwt);
        
        return $response;
    }
}
