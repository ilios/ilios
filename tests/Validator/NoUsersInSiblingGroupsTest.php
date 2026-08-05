<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\NoUsersInSiblingGroups;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

#[CoversClass(NoUsersInSiblingGroups::class)]
final class NoUsersInSiblingGroupsTest extends TestCase
{
    public function testTargetsTheClass(): void
    {
        $constraint = new NoUsersInSiblingGroups();

        $this->assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }

    public function testHasDefaultMessage(): void
    {
        $constraint = new NoUsersInSiblingGroups();

        $this->assertSame(
            'A user cannot be added to more than one sibling group.',
            $constraint->message
        );
    }
}
