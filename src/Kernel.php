<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function date_default_timezone_set;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Hook into creation and __wakeup of the symfony kernel in order to
     * make our own customizations.
     */
    public function __construct(string $environment, bool $debug)
    {
        // Force a UTC timezone on everyone
        date_default_timezone_set('UTC');
        parent::__construct($environment, $debug);
    }
}
