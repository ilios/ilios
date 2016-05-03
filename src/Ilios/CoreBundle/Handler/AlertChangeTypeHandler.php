<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AlertChangeTypeType;
use Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManager;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class AlertChangeTypeHandler
 * @package Ilios\CoreBundle\Handler
 */
class AlertChangeTypeHandler extends AlertChangeTypeManager
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
     * @return AlertChangeTypeInterface
     */
    public function post(array $parameters)
    {
        $alertChangeType = $this->createAlertChangeType();

        return $this->processForm($alertChangeType, $parameters, 'POST');
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param array $parameters
     *
     * @return AlertChangeTypeInterface
     */
    public function put(
        AlertChangeTypeInterface $alertChangeType,
        array $parameters
    ) {
        return $this->processForm(
            $alertChangeType,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param array $parameters
     *
     * @return AlertChangeTypeInterface
     */
    public function patch(
        AlertChangeTypeInterface $alertChangeType,
        array $parameters
    ) {
        return $this->processForm(
            $alertChangeType,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AlertChangeTypeInterface
     */
    protected function processForm(
        AlertChangeTypeInterface $alertChangeType,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            AlertChangeTypeType::class,
            $alertChangeType,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
