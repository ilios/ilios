<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\SessionType;
use Ilios\CoreBundle\Entity\Manager\SessionManager;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionHandler
 * @package Ilios\CoreBundle\Handler
 */
class SessionHandler extends SessionManager
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
     * @return SessionInterface
     */
    public function post(array $parameters)
    {
        $session = $this->createSession();

        return $this->processForm($session, $parameters, 'POST');
    }

    /**
     * @param SessionInterface $session
     * @param array $parameters
     *
     * @return SessionInterface
     */
    public function put(
        SessionInterface $session,
        array $parameters
    ) {
        return $this->processForm(
            $session,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param SessionInterface $session
     * @param array $parameters
     *
     * @return SessionInterface
     */
    public function patch(
        SessionInterface $session,
        array $parameters
    ) {
        return $this->processForm(
            $session,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param SessionInterface $session
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SessionInterface
     */
    protected function processForm(
        SessionInterface $session,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            SessionType::class,
            $session,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
