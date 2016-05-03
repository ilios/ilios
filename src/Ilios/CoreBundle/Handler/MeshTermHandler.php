<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshTermType;
use Ilios\CoreBundle\Entity\Manager\MeshTermManager;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * Class MeshTermHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshTermHandler extends MeshTermManager
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
     * @return MeshTermInterface
     */
    public function post(array $parameters)
    {
        $meshTerm = $this->createMeshTerm();

        return $this->processForm($meshTerm, $parameters, 'POST');
    }

    /**
     * @param MeshTermInterface $meshTerm
     * @param array $parameters
     *
     * @return MeshTermInterface
     */
    public function put(
        MeshTermInterface $meshTerm,
        array $parameters
    ) {
        return $this->processForm(
            $meshTerm,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshTermInterface $meshTerm
     * @param array $parameters
     *
     * @return MeshTermInterface
     */
    public function patch(
        MeshTermInterface $meshTerm,
        array $parameters
    ) {
        return $this->processForm(
            $meshTerm,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshTermInterface $meshTerm
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshTermInterface
     */
    protected function processForm(
        MeshTermInterface $meshTerm,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            MeshTermType::class,
            $meshTerm,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
