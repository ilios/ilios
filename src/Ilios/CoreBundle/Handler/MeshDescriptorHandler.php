<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshDescriptorType;
use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Class MeshDescriptorHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshDescriptorHandler extends MeshDescriptorManager
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
     * @return MeshDescriptorInterface
     */
    public function post(array $parameters)
    {
        $meshDescriptor = $this->createMeshDescriptor();

        return $this->processForm($meshDescriptor, $parameters, 'POST');
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param array $parameters
     *
     * @return MeshDescriptorInterface
     */
    public function put(
        MeshDescriptorInterface $meshDescriptor,
        array $parameters
    ) {
        return $this->processForm(
            $meshDescriptor,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param array $parameters
     *
     * @return MeshDescriptorInterface
     */
    public function patch(
        MeshDescriptorInterface $meshDescriptor,
        array $parameters
    ) {
        return $this->processForm(
            $meshDescriptor,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshDescriptorInterface
     */
    protected function processForm(
        MeshDescriptorInterface $meshDescriptor,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            MeshDescriptorType::class,
            $meshDescriptor,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
