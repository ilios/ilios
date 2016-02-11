<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AuthenticationType;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\AuthenticationInterface;

/**
 * Class AuthenticationHandler
 * @package Ilios\CoreBundle\Handler
 */
class AuthenticationHandler extends AuthenticationManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param Registry $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Registry $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return AuthenticationInterface
     */
    public function post(array $parameters)
    {
        $authentication = $this->createAuthentication();

        return $this->processForm($authentication, $parameters, 'POST');
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param array $parameters
     *
     * @return AuthenticationInterface
     */
    public function put(
        AuthenticationInterface $authentication,
        array $parameters
    ) {
        return $this->processForm(
            $authentication,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param array $parameters
     *
     * @return AuthenticationInterface
     */
    public function patch(
        AuthenticationInterface $authentication,
        array $parameters
    ) {
        return $this->processForm(
            $authentication,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AuthenticationInterface
     */
    protected function processForm(
        AuthenticationInterface $authentication,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new AuthenticationType(),
            $authentication,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
