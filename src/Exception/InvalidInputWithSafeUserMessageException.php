<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidInputWithSafeUserMessageException extends BadRequestHttpException
{
}
