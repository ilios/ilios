<?php

namespace Ilios\CoreBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidFormException extends BadRequestHttpException
{
    protected $form;

    public function __construct($message, $form = null)
    {
        parent::__construct($message);
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }
}