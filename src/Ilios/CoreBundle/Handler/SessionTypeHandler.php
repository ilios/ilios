<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\SessionTypeType;
use Ilios\CoreBundle\Entity\Manager\SessionTypeManager;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypeHandler
 * @package Ilios\CoreBundle\Handler
 */
class SessionTypeHandler extends SessionTypeManager
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
     * @return SessionTypeInterface
     */
    public function post(array $parameters)
    {
        $sessionType = $this->createSessionType();

        return $this->processForm($sessionType, $parameters, 'POST');
    }

    /**
     * @param SessionTypeInterface $sessionType
     * @param array $parameters
     *
     * @return SessionTypeInterface
     */
    public function put(
        SessionTypeInterface $sessionType,
        array $parameters
    ) {
        return $this->processForm(
            $sessionType,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param SessionTypeInterface $sessionType
     * @param array $parameters
     *
     * @return SessionTypeInterface
     */
    public function patch(
        SessionTypeInterface $sessionType,
        array $parameters
    ) {
        return $this->processForm(
            $sessionType,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param SessionTypeInterface $sessionType
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SessionTypeInterface
     */
    protected function processForm(
        SessionTypeInterface $sessionType,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            SessionTypeType::class,
            $sessionType,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
