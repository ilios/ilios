<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Utils\Utils;
use InvalidArgumentException;

use function assert;
use function checkdate;
use function is_string;
use function preg_match;
use function substr;
use function strpos;

class DateTimeType extends CustomScalarType
{
    private const string NAME           = 'DateTime';
    private const string DESCRIPTION    = 'Represents time data, represented as an ISO-8601 encoded UTC date string.';
    private const string RFC_3339_REGEX = '~^(\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][\d]|3[01])T([01][\d]|2[0-3]):' .
                                   '([0-5][\d]):([0-5][\d]|60))(\.\d{1,})?(([Z])|([+|-]([01][\d]|2[0-3]):' .
                                   '[0-5][\d]))$~';
    public string $name = self::NAME;
    public ?string $description = self::DESCRIPTION;
    public static DateTimeType $instance;

    public function __construct()
    {
        parent::__construct(
            [
                'name'        => self::NAME,
                'description' => self::DESCRIPTION,
            ]
        );
    }

    public static function getInstance(): DateTimeType
    {
        if (!isset(self::$instance)) {
            self::$instance = new DateTimeType();
        }
        return self::$instance;
    }

    public function serialize(mixed $value): string
    {
        if (! $value instanceof DateTimeInterface) {
            throw new InvariantViolation(
                'DateTime is not an instance of DateTimeImmutable nor DateTime: ' . Utils::printSafe($value)
            );
        }

        return $value->format(DateTimeInterface::ATOM);
    }

    public function parseValue(mixed $value): DateTimeImmutable
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException();
        }

        if (! $this->validateDatetime($value)) {
            throw new InvalidArgumentException(sprintf(
                'DateTime type expects input value to be ISO 8601 compliant. Given invalid value "%s"',
                (string) $value
            ));
        }

        return new DateTimeImmutable($value);
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null): ?DateTimeImmutable
    {
        if (! $valueNode instanceof StringValueNode) {
            return null;
        }

        return $this->parseValue($valueNode->value);
    }

    private function validateDatetime(string $value): bool
    {
        if (preg_match(self::RFC_3339_REGEX, $value) !== 1) {
            return false;
        }

        $tPosition = strpos($value, 'T');
        assert($tPosition !== false);

        return $this->validateDate(substr($value, 0, $tPosition));
    }

    private function validateDate(string $date): bool
    {
        // Verify the correct number of days for the month contained in the date-string.
        $year  = (int) substr($date, 0, 4);
        $month = (int) substr($date, 5, 2);
        $day   = (int) substr($date, 8, 2);

        return checkdate($month, $day, $year);
    }
}
