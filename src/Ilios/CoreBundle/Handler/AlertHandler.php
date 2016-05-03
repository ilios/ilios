<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AlertType;
use Ilios\CoreBundle\Entity\Manager\AlertManager;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Class AlertHandler
 * @package Ilios\CoreBundle\Handler
 */
class AlertHandler extends AlertManager
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
     * @return AlertInterface
     */
    public function post(array $parameters)
    {
        $alert = $this->createAlert();

        return $this->processForm($alert, $parameters, 'POST');
    }

    /**
     * @param AlertInterface $alert
     * @param array $parameters
     *
     * @return AlertInterface
     */
    public function put(
        AlertInterface $alert,
        array $parameters
    ) {
        return $this->processForm(
            $alert,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AlertInterface $alert
     * @param array $parameters
     *
     * @return AlertInterface
     */
    public function patch(
        AlertInterface $alert,
        array $parameters
    ) {
        return $this->processForm(
            $alert,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AlertInterface $alert
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AlertInterface
     */
    protected function processForm(
        AlertInterface $alert,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            AlertType::class,
            $alert,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
