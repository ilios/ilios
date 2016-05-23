<?php

namespace Ilios\CoreBundle\Form;

use Ilios\CoreBundle\Entity\Manager\ManagerInterface;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class Handler
 * @package Ilios\CoreBundle\Form
 */
class Handler implements HandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ManagerInterface;
     */
    protected $manager;

    /**
     * @var string
     */
    protected $typeClass;

    /**
     * @param ManagerInterface $manager
     * @param FormFactoryInterface $formFactory
     * @param string $typeClass
     */
    public function __construct(ManagerInterface $manager, FormFactoryInterface $formFactory, $typeClass)
    {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->typeClass = $typeClass;
    }

    /**
     * @inheritdoc
     */
    public function post(array $parameters)
    {
        $entity = $this->manager->create();
        return $this->processForm($entity, $parameters, 'POST');
    }

    /**
     * @inheritdoc
     */
    public function put($entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PUT');
    }

    /**
     * @inheritdoc
     */
    public function patch($entity, array $parameters)
    {
        return $this->processForm($entity, $parameters, 'PATCH');
    }

    /**
     * @param object $entity
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return object
     */
    protected function processForm($entity, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create($this->typeClass, $entity, array('method' => $method));

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
