<?php

declare(strict_types=1);

namespace App\Monitor;

use Composer\Autoload\ClassLoader;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;

class Composer implements CheckInterface
{
    /**
     * @inheritdoc
     */
    public function check()
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

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'Composer Autoload Setup';
    }
}
