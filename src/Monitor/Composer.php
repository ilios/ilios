<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Result\ResultInterface;
use Composer\Autoload\ClassLoader;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;

class Composer implements CheckInterface
{
    public function check(): ResultInterface
    {
        // get the composer autoloader so we can check it's options
        /** @var ClassLoader $loader */
        $loader = (include __DIR__ . '/../../vendor/autoload.php');
        $prefixes = $loader->getPrefixesPsr4();
        if (
            !$loader->isClassMapAuthoritative() ||
            array_key_exists('App\\Tests\\', $prefixes)
        ) {
            return new Failure("is not optimized. Run `composer dump-autoload --no-dev --classmap-authoritative`");
        }



        return new Success('is correct');
    }

    public function getLabel(): string
    {
        return 'Composer Autoload Setup';
    }
}
