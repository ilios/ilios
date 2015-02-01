<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\MeshDescriptorType;
use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

class MeshDescriptorHandler extends MeshDescriptorManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
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
    public function put(MeshDescriptorInterface $meshDescriptor, array $parameters)
    {
        return $this->processForm($meshDescriptor, $parameters, 'PUT');
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param array $parameters
     *
     * @return MeshDescriptorInterface
     */
    public function patch(MeshDescriptorInterface $meshDescriptor, array $parameters)
    {
        return $this->processForm($meshDescriptor, $parameters, 'PATCH');
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshDescriptorInterface
     */
    protected function processForm(MeshDescriptorInterface $meshDescriptor, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new MeshDescriptorType(), $meshDescriptor, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $meshDescriptor = $form->getData();
            $this->updateMeshDescriptor($meshDescriptor, true);

            return $meshDescriptor;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
