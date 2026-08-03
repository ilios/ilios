<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exception\InvalidInputWithSafeUserMessageException;
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
    ): void {
        foreach ($entities as $entity) {
            $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);
        }
    }

    public function validateAndAuthorizeEntity(
        object $entity,
        string $permission,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $this->validateEntity($entity, $validator);
        if (!$authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }
    }

    public function validateEntity(
        object $entity,
        ValidatorInterface $validator
    ): void {
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($validator->validate($entity) as $violation) {
            $property = $violation->getPropertyPath();
            $message = $violation->getMessage();
            $errors[] = "Error in *{$property}*: {$message}";
        }
        if (count($errors)) {
            $errorsString = implode("\n", $errors);
            throw new InvalidInputWithSafeUserMessageException($errorsString);
        }
    }
}
