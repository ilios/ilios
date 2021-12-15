<?php

declare(strict_types=1);

namespace App\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

trait GetFakerTrait
{
    private FakerGenerator $generatedFaker;

    protected function getFaker(): FakerGenerator
    {
        if (!isset($this->generatedFaker)) {
            $this->generatedFaker = FakerFactory::create();
            $this->generatedFaker->seed(17105);
        }

        return $this->generatedFaker;
    }
}
