<?php
namespace Ilios\AuthenticationBundle\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait AuthenticationService
{
    protected function createSuccessResponseFromJWT($jwt)
    {
        $response =  new JsonResponse(array(
            'status' => 'success',
            'errors' => [],
            'jwt' => $jwt,
        ), JsonResponse::HTTP_OK);
        $response->headers->set('X-JWT-TOKEN', $jwt);
        
        return $response;
    }
}
