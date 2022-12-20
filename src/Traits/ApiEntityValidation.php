<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ApiEntityValidation
{
    public function validateAndAuthorizeEntities(
        array $entities,
        string $permission,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        foreach ($entities as $entity) {
            $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);
        }
    }

    public function validateAndAuthorizeEntity(
        object $entity,
        string $permission,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->validateEntity($entity, $validator);
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
    }

    public function validateEntity(
        object $entity,
        ValidatorInterface $validator
    ) {
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($validator->validate($entity) as $violation) {
            $property = $violation->getPropertyPath();
            $message = $violation->getMessage();
            $errors[] = "Error in *{$property}*: {$message}";
        }
        if (count($errors)) {
            $errorsString = implode("\n", $errors);
            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
    }
}
