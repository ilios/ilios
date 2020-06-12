<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

AnnotationReader::addGlobalIgnoredName('covers');
AnnotationReader::addGlobalIgnoredName('group');
