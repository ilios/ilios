<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidInputWithSafeUserMessageException extends BadRequestHttpException
{
}
