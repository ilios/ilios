<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Service\GraphQL\TypeResolver;
use App\Service\GraphQL\TypeRegistry;
use GraphQL\Error\DebugFlag;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function json_decode;
use function json_encode;

class GraphQL
{
    #[Route('/api/graphql')]
    public function index(Request $request, TypeRegistry $typeRegistry, TypeResolver $resolver): Response
    {
        $types = $typeRegistry->getTypes();
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => $types,
        ]);
        $schema = new Schema([
            'query' => $queryType
        ]);
        $input = json_decode($request->getContent() ?? '', true);
        $variableValues = $input['variables'] ?? null;
        $result = \GraphQL\GraphQL::executeQuery(
            $schema,
            $input['query'] ?? null,
            null,
            null,
            $variableValues,
            null,
            $resolver,
        );
        return JsonResponse::fromJsonString(json_encode($result->toArray(DebugFlag::RETHROW_INTERNAL_EXCEPTIONS)));
    }
}
