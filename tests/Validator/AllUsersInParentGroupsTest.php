<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\AllUsersInParentGroups;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

#[CoversClass(AllUsersInParentGroups::class)]
final class AllUsersInParentGroupsTest extends TestCase
{
    public function testTargetsTheClass(): void
    {
        $constraint = new AllUsersInParentGroups();

        $this->assertSame(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }

    public function testHasDefaultMessage(): void
    {
        $constraint = new AllUsersInParentGroups();

        $this->assertSame(
            'Every user in a learner group must also be a user in its parent group.',
            $constraint->message
        );
    }
}
