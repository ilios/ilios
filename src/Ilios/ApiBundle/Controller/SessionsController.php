<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SessionsController
 */
class SessionsController extends ApiController
{
    /**
     * Extract and process DateTime properties
     * @inheritdoc
     */
    protected function extractParameters(Request $request)
    {
        $parameters = parent::extractParameters($request);
        if (array_key_exists('updatedAt', $parameters['criteria'])) {
            $parameters['criteria']['updatedAt'] = new \DateTime($parameters['criteria']['updatedAt']);
        }

        return $parameters;
    }
}
