<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshQualifierType;
use Ilios\CoreBundle\Entity\Manager\MeshQualifierManager;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class MeshQualifierHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshQualifierHandler extends MeshQualifierManager
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
     * @return MeshQualifierInterface
     */
    public function post(array $parameters)
    {
        $meshQualifier = $this->createMeshQualifier();

        return $this->processForm($meshQualifier, $parameters, 'POST');
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param array $parameters
     *
     * @return MeshQualifierInterface
     */
    public function put(
        MeshQualifierInterface $meshQualifier,
        array $parameters
    ) {
        return $this->processForm(
            $meshQualifier,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param array $parameters
     *
     * @return MeshQualifierInterface
     */
    public function patch(
        MeshQualifierInterface $meshQualifier,
        array $parameters
    ) {
        return $this->processForm(
            $meshQualifier,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshQualifierInterface
     */
    protected function processForm(
        MeshQualifierInterface $meshQualifier,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new MeshQualifierType(),
            $meshQualifier,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $meshQualifier = $form->getData();
            $this->updateMeshQualifier($meshQualifier, true, ('PUT' === $method || 'PATCH' === $method));

            return $meshQualifier;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
